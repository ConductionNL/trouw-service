<?php

namespace App\Service;

use App\Entity\WebHook;
use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TrouwService
{
    private $em;
    private $commonGroundService;
    private $params;

    public function __construct(EntityManagerInterface $em, CommonGroundService $commonGroundService, ParameterBagInterface $params)
    {
        $this->em = $em;
        $this->commonGroundService = $commonGroundService;
        $this->params = $params;
    }

    public function webHook(WebHook $webHook)
    {
        if($webHook->getTask() && $task = $this->commonGroundService->getResource($webHook->getTask())){
            $this->executeTask($task, $webHook);
        }
        else{
            $webhook = $this->sendConfirmation($webHook);

        }
        $this->em->persist($webHook);
        $this->em->flush();
    }

    public function sendConfirmation(Webhook $webHook){
        $resource = $this->commonGroundService->getResource($webHook->getResource());
        $result = [];
//        var_dump($resource['organization']);
        $message['service'] = $this->commonGroundService->getResourceList(['component'=>'bs','type'=>'services'],"?type=mailer&organization={$resource['organization']}")['hydra:member'][0]['@id'];
        $message['status'] = 'queued';
        $organization = $this->commonGroundService->getResource($resource['organization']);

        if($organization['contact']){
            $message['sender'] = $organization['contact'];
        }
        $submitters = $resource['submitters'];
        $message['content'] = $this->commonGroundService->getResource(['component'=>'wrc','type'=>'applications','id'=>"{$this->params->get('app_id')}/e-mail-wijziging"])['@id'];
        foreach($submitters as $submitter){
            if(key_exists('person', $submitter)  && $submitter['person'] != null){
                $message['reciever'] = $this->commonGroundService->getResource($submitter['person']);
                if(!key_exists('sender', $message)){
                    $message['sender'] = $message['reciever'];
                }
                $message['data'] = ['resource'=>$resource, 'contact'=>$message['reciever'], 'organization'=>$message['sender']];
                $result[] = $this->commonGroundService->createResource($message, ['component'=>'bs', 'type'=>'messages'])['@id'];
            }
        }

        if(key_exists('partners', $resource['properties'])){
            foreach($resource['properties']['partners'] as $partner){
                if($partner = $this->commonGroundService->isResource($partner)){
                    $message['reciever'] = $partner["contact"];
                    if(!key_exists('sender', $message)){
                        $message['sender'] = $message['reciever'];
                    }
                    $message['data'] = ['resource'=>$resource, 'sender'=>$organization, 'receiver'=>$this->commonGroundService->getResource($message['reciever'])];
                    $result[] = $this->commonGroundService->createResource($message, ['component'=>'bs', 'type'=>'messages'])['@id'];
                }
            }
        }
        $webHook->setResult($result);
        return $webHook;
    }




    // Task execution from here
    public function executeTask(array $task, Webhook $webHook){
        $resource = $this->commonGroundService->getResource($webHook->getResource());

        switch ($task['code']) {
            case 'update':
                $resource = $this->update($task, $resource);
                break;
            case 'reminder_indienen':
                $resource = $this->reminderIndienen($task, $resource);
                break;
            case 'reminder_instemmen':
                $resource = $this->reminderInstemmen($task, $resource);
                break;
            case 'reminder_betalen':
                $resource = $this->reminderBetalen($task, $resource);
                break;
            case 'verlopen_reservering':
                $resource = $this->verlopenReservering($task, $resource);
                break;
            case 'verlopen_huwelijk':
                $resource = $this->verlopenHuwelijk($task, $resource);
                break;
            case 'ingediend_huwelijk':
                $resource = $this->ingediendHuwelijk($task, $resource);
                break;
            default:
                break;
        }

        $this->commonGroundService->saveResource($resource);
    }

    public function update(array $task, array $resource)
    {
        // We want to force product shizle
        $ceremonieOfferId = $this->commonGroundService->getUuidFromUrl($resource['properties']['plechtigheid']);
        switch ($ceremonieOfferId) {
            case '1ba1772b-cc8a-4808-ad1e-f9b3c93bdebf': // Flits huwelijks
                $resource['properties']['ambtenaar'] = $this->commonGroundService->cleanUrl(['component'=>'pdc', 'type'=>'offers', 'id'=>'55af09c8-361b-418a-af87-df8f8827984b']);
                $resource['properties']['locatie'] = $this->commonGroundService->cleanUrl(['component'=>'pdc', 'type'=>'offers', 'id'=>'9aef22c4-0c35-4615-ab0e-251585442b55']);
                break;
            case '77f6419d-b264-4898-8229-9916d9deccee': // Gratis trouwen
                $resource['properties']['ambtenaar'] = $this->commonGroundService->cleanUrl(['component'=>'pdc', 'type'=>'offers', 'id'=>'55af09c8-361b-418a-af87-df8f8827984b']);
                $resource['properties']['locatie'] = $this->commonGroundService->cleanUrl(['component'=>'pdc', 'type'=>'offers', 'id'=>'7a3489d5-2d2c-454b-91c9-caff4fed897f']);
                break;
            case '2b9ba0a9-376d-45e2-aa83-809ef07fa104': // Eenvoudig trouwen
                $resource['properties']['ambtenaar'] = $this->commonGroundService->cleanUrl(['component'=>'pdc', 'type'=>'offers', 'id'=>'55af09c8-361b-418a-af87-df8f8827984b']);
                $resource['properties']['locatie'] = $this->commonGroundService->cleanUrl(['component'=>'pdc', 'type'=>'offers', 'id'=>'7a3489d5-2d2c-454b-91c9-caff4fed897f']);
                break;
            case 'bfeb9399-fce6-49b8-a047-70928f3611fb': // Uitgebreid trouwen
                // In het geval van uitgebreid trouwen hoeven we niks te forceren
                break;
        }

        // Verlopen reservering
        $newTask = [];
        $newTask['code'] = 'verlopen_reservering';
        $newTask['resource'] = $resource['@id'];
        $newTask['endpoint'] = $task['endpoint'];
        $newTask['type'] = 'POST';

        // Lets set the time to trigger
        $dateToTrigger = new \DateTime();
        $dateToTrigger->add(new \DateInterval('P5D')); // Verloopt over 5 dagen
        $newTask['dateToTrigger'] = $dateToTrigger->format('Y-m-d H:i:s');
        $this->commonGroundService->saveResource($newTask, ['component'=>'qc', 'type'=>'tasks']);

        //verlopen huwelijk
        $newTask = [];
        $newTask['code'] = 'verlopen_huwelijk';
        $newTask['resource'] = $resource['@id'];
        $newTask['endpoint'] = $task['endpoint'];
        $newTask['type'] = 'POST';

        // Lets set the time to trigger
        $dateToTrigger = new \DateTime();
        $dateToTrigger->add(new \DateInterval('P1Y')); // verloopt over 1 jaar
        $newTask['dateToTrigger'] = $dateToTrigger->format('Y-m-d H:i:s');
        $this->commonGroundService->saveResource($newTask, ['component'=>'qc', 'type'=>'tasks']);

        //ingediend huwelijk
        $newTask = [];
        $newTask['code'] = 'ingediend_huwelijk';
        $newTask['resource'] = $resource['@id'];
        $newTask['endpoint'] = $task['endpoint'];
        $newTask['type'] = 'POST';

        // Lets set the time to trigger
        $dateToTrigger = new \DateTime();
        $dateToTrigger->add(new \DateInterval('P2W')); // verloopt over 2 weken
        $newTask['dateToTrigger'] = $dateToTrigger->format('Y-m-d H:i:s');
        $this->commonGroundService->saveResource($newTask, ['component'=>'qc', 'type'=>'tasks']);

        // Reminder indienen
        $newTask = [];
        $newTask['code'] = 'reminder_indienen';
        $newTask['resource'] = $resource['@id'];
        $newTask['endpoint'] = $task['endpoint'];
        $newTask['type'] = 'POST';

        // Lets set the time to trigger
        $dateToTrigger = new \DateTime();
        $dateToTrigger->add(new \DateInterval('P11D')); // verloopt over 11 dagen
        $newTask['dateToTrigger'] = $dateToTrigger->format('Y-m-d H:i:s');
        $this->commonGroundService->saveResource($newTask, ['component'=>'qc', 'type'=>'tasks']);

        // Reminder indienen
        $newTask = [];
        $newTask['code'] = 'reminder_instemmen';
        $newTask['resource'] = $resource['@id'];
        $newTask['endpoint'] = $task['endpoint'];
        $newTask['type'] = 'POST';

        // Lets set the time to trigger
        $dateToTrigger = new \DateTime();
        $dateToTrigger->add(new \DateInterval('P1W')); // verloopt over 1 week
        $newTask['dateToTrigger'] = $dateToTrigger->format('Y-m-d H:i:s');
        $this->commonGroundService->saveResource($newTask, ['component'=>'qc', 'type'=>'tasks']);

        // Reminder betalen
        $newTask = [];
        $newTask['code'] = 'reminder_betalen';
        $newTask['resource'] = $resource['@id'];
        $newTask['endpoint'] = $task['endpoint'];
        $newTask['type'] = 'POST';

        // Lets set the time to trigger
        $dateToTrigger = new \DateTime();
        $dateToTrigger->add(new \DateInterval('P1W')); // verloopt over 1 week
        $newTask['dateToTrigger'] = $dateToTrigger->format('Y-m-d H:i:s');
        $this->commonGroundService->saveResource($newTask, ['component'=>'qc', 'type'=>'tasks']);

        return $resource;
    }

    public function reminderIndienen(array $task, array $resource)
    {
        // valideren of het moet gebeuren
        if (
            $resource['status'] == 'incomplete'

        ) {
            return; // Eigenlijk moet je hier een error gooien maar goed
        }

        // dus ga mail versturen

        return $resource;
    }

    public function reminderBetalen(array $task, array $resource)
    {
        // valideren of het moet gebeuren
        if (
            $resource['status'] == '!submitted' &&
            $resource['properties']['betalen'] == false

        ) {
            return; // Eigenlijk moet je hier een error gooien maar goed
        }

        //setting reminder for next week
        $newTask = [];
        $newTask['code'] = 'reminder_betalen';
        $newTask['resource'] = $resource['@id'];
        $newTask['endpoint'] = $task['endpoint'];
        $newTask['type'] = 'POST';

        // Lets set the time to trigger
        $dateToTrigger = new \DateTime();
        $dateToTrigger->add(new \DateInterval('P1W')); // verloopt over 1 week
        $newTask['dateToTrigger'] = $dateToTrigger->format('Y-m-d H:i:s');
        $this->commonGroundService->saveResource($newTask, ['component'=>'qc', 'type'=>'tasks']);

        // dus ga mail versturen

        return $resource;
    }

    public function reminderInstemmen(array $task, array $resource)
    {
        // valideren of het moet gebeuren
        $ingestemd = true;
        foreach ($resource['properties']['getuigen'] as  $getuige) {
            $check = $this->commonGroundService->getResource($getuige);

            if ($check['status'] != 'granted' && $check['status'] != 'submitted' && $ingestemd != false) {
                $ingestemd = false;
            }
        }

        if (
            $resource['status'] == 'submitted' &&
            $ingestemd = true
        ) {
            return; // Eigenlijk moet je hier een error gooien maar goed
        }

        //setting reminder for next week
        $newTask = [];
        $newTask['code'] = 'reminder_instemmen';
        $newTask['resource'] = $resource['@id'];
        $newTask['endpoint'] = $task['endpoint'];
        $newTask['type'] = 'POST';

        // Lets set the time to trigger
        $dateToTrigger = new \DateTime();
        $dateToTrigger->add(new \DateInterval('P1W')); // verloopt over 1 week
        $newTask['dateToTrigger'] = $dateToTrigger->format('Y-m-d H:i:s');
        $this->commonGroundService->saveResource($newTask, ['component'=>'qc', 'type'=>'tasks']);

        // dus ga mail versturen

        return $resource;
    }

    public function verlopenReservering(array $task, array $resource)
    {
        // valideren of het moet gebeuren

        return $resource;
    }

    public function verlopenHuwelijk(array $task, array $resource)
    {
        // valideren of het moet gebeuren
        if (
            $resource['status'] != 'complete' ||
            $resource['status'] != 'cancelled'
        ) {
            return; // Eigenlijk moet je hier een error gooien maar goed
        }

        $resource['properties']['datum'] == null;

        return $resource;
    }

    public function ingediendHuwelijk(array $task, array $resource)
    {
        // valideren of het moet gebeuren
        if (
            $resource['status'] != 'incomplete' ||
            $resource['status'] != 'cancelled'
        ) {
            return; // Eigenlijk moet je hier een error gooien maar goed
        }

        $resource['properties']['datum'] == null;

        return $resource;
    }

    public function sendReminder(array $resource)
    {
        // bla bal bla

        return $resource;
    }
}

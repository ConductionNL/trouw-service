<?php
namespace App\Service;

use App\Entity\WebHook;
use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class TrouwService
{
    private $em;
    private $commonGroundService;

    public function __construct(EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $this->em = $em;
        $this->commonGroundService = $commonGroundService;
    }

    public function webHook($task $resource){


        switch ($task['code']) {
            case 'update':
                $resource = $this->update($task $resource);
                break;
            case 'reminder_trouwen':
                $resource = $this->reminderTrouwen($task $resource);
                break;
            case 2:
                echo "i equals 2";
                break;
            default:
               echo "i is not equal to 0, 1 or 2";
        }

        // dit pas live gooide nadat we in de event hook optioneel hebben gemaakt
        $this->commongroundService->saveResource($resource);
    }

    public function update(araay $task, array $resource)
    {
        // Verlopen reservering
        $task = [];
        $task['code'] = 'verlopen_reservering';
        $task['resource'] = $resource['@id'];
        $task['endpoint'] = $task['endpoint'];
        $task['type'] = 'POST';

        // Lets set the time to trigger
        $dateToTrigger = new \DateTime();
        $dateToTrigger->add(new \DateInterval('P5D')); // Verloopt over 5 dagen
        $task['dateToTrigger'] = $dateToTrigger->format('Y-m-d H:i:s');

        // Reminder trouwen
        $task = [];
        $task['code'] = 'reminder_trouwen';
        $task['resource'] = $resource['@id'];
        $task['endpoint'] = $task['endpoint'];
        $task['type'] = 'POST';

        // however ik een week va te voren berken
        $task['dateToTrigger'] = $dateToTrigger->format('Y-m-d H:i:s');

        // Eerste werkdag van te voren, akte printen



        //

        return $resource;
    }


    public function reminderTrouwen(araay $task, array $resource)
    {
        // valideren of het moet gebeuren
        if(
            $resource['status'] != 'retracted'
        )
        {
            return; // Eigenlijk moet je hier een error gooien maar goed
        }

        // dus ga mail versturen
        
        return $resource;
    }

    public function sendReminder(array $resource)
    {
        // bla bal bla

        return $resource;
    }

    public function getWebHook($taskUri, $resourceUri){
        $client = new Client();
        $api_key = '45c1a4b6-59d3-4a6e-86bf-88a872f35845';

        $requestTask = new Request('GET', $taskUri, ['headers' => [
            'Authorization' => $api_key
        ]]);
        $task = $client->send($requestTask, ['timeout' => 2]);

        $requestResource = new Request('GET', $resourceUri, ['headers' => [
            'Authorization' => $api_key
        ]]);
        $resource = $client->send($requestResource, ['timeout' => 2]);
    }


}

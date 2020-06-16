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

    public function webHook($task, $resource){


        switch($task['code']) {
            case "update":
                $resource = $this->update($task, $resource);
                break;
            case "reminder_trouwen":
                $resource = $this->reminderTrouwen($task, $resource);
                break;
            case "verlopen_reservering":
                $resource = $this->verlopenReservering($task, $resource);
                break;
            default:
               echo "i is not equal to 0, 1 or 2";
        }

        // dit pas live gooide nadat we in de event hook optioneel hebben gemaakt
        $this->commonGroundService->saveResource($resource);
    }



    public function update(array $task, array $resource)
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


    public function reminderTrouwen(array $task, array $resource)
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

    public function verlopenReservering(array $task, array $resource)
    {
        // valideren of het moet gebeuren


        return $resource;
    }

    public function sendReminder(array $resource)
    {
        // bla bal bla

        return $resource;
    }





}

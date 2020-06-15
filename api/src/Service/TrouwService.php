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

    public function getWebHook($taskUri, $resourceUri){
        $client = new Client();

        $requestTask = new Request('GET', $taskUri, [
        'headers' => [ 'Authorization' => '45c1a4b6-59d3-4a6e-86bf-88a872f35845']]);
        $task = $client->send($requestTask, ['timeout' => 2]);

        $requestResource = new Request('GET', $resourceUri, [
        'headers' => [ 'Authorization' => '45c1a4b6-59d3-4a6e-86bf-88a872f35845']] );
        $resource = $client->send($requestResource, ['timeout' => 2]);
    }




}

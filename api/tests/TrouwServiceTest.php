<?php

namespace App\Tests;

use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use App\Service\TrouwService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TrouwServiceTest extends TestCase
{
    private $trouwService;


    public function __construct(TrouwService $trouwService)
    {
        $this->trouwService = $trouwService;

    }

    public function testSomething()
    {

    }

    public function testWebhook()
    {

    }
}

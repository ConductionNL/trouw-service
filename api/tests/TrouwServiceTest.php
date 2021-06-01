<?php

namespace App\Tests;

use App\Service\TrouwService;
use PHPUnit\Framework\TestCase;

class TrouwServiceTest extends TestCase
{
    private $trouwService;

    public function __construct(TrouwService $trouwService)
    {
        $this->trouwService = $trouwService;
    }

    public function testSomething()
    {
        $test = 0;
        if ($test == 0) {
            $test = 5;
        }

        if ($test ==4) {
            $test = 5;
        }

        echo $test;

    }

}

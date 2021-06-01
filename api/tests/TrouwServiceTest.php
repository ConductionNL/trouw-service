<?php

namespace App\Tests;

use App\Subscriber\WebHookSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Event\ViewEvent;

class TrouwServiceTest extends TestCase
{
    private $webhookSubscriber;
    private $view;


    public function __construct(WebHookSubscriber $webHookSubscriber, ViewEvent $view)
    {
        $this->webhookSubscriber = $webHookSubscriber;
        $this->view = $view;
    }

    public function testSomething()
    {

    }

    public function testWebhook()
    {
        $this->assertIsNotInt($this->webhookSubscriber->webHook($this->view));
    }
}

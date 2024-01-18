<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LessonCompletedSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            // 'kernel.request' => 'onKernelRequest'
        );
    }
}

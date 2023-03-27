<?php

namespace Framework\EventSubscriber;

use Framework\Event\ResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ContentLengthSubscriber implements EventSubscriberInterface
{
    public function onResponse(ResponseEvent $event)
    {
        $response = $event->getResponse();
        $headers = $response->headers;
        if (!$headers->has('Content-Length') && !$headers->has('Transfer-Encoding')) {
            $headers->set('Content-Length', strlen($response->getContent()));
        }
    }


    public static function getSubscribedEvents()
    {
        return ['response' => ['onResponse', 0]];
    }


}
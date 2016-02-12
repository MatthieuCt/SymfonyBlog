<?php

namespace BlogBundle\Listener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class CookieListener
{
    public function checkCookies(GetResponseEvent $event)
    {
        $response = new Response();
        $cookies = $event->getRequest()->cookies;

//        print_r($cookies);

    }
}

<?php

namespace BlogBundle\Listener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class CookieListener
{
    public function checkCookies(GetResponseEvent $event)
    {
//        $cookies = $event->getRequest()->cookies->all();
        $cookie = $event->getRequest()->cookies->get('cookie');
        if (is_null($cookie)) {
            $event->getRequest()->query->set('cookie', $cookie);
        }
    }
}

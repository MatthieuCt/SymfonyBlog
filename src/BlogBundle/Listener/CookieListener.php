<?php

namespace BlogBundle\Listener;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class CookieListener
{
    public function checkCookies(GetResponseEvent $event)
    {
        if (!isset($_COOKIE['cookie'])) {
            setcookie('cookie', true);
        }
    }
}

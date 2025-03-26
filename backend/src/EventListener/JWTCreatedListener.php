<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event): void
    {

        $user = $event->getUser();

        if (!$user instanceof \App\Entity\User) {
            return;
        }

        $payload = $event->getData();
        $payload['id'] = $user->getId();
        $payload['first_name'] = $user->getFirstName();
        $payload['last_name'] = $user->getLastName();
        $payload['email'] = $user->getEmail();
        $payload['company'] = $user->getCompany()?->getName();

        $event->setData($payload);
    }
}
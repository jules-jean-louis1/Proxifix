<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();

        if (! $user instanceof \App\Entity\User) {
            return;
        }

        $payload = $event->getData();
        $payload['id'] = $user->getId();
        $payload['first_name'] = $user->getFirstName();
        $payload['last_name'] = $user->getLastName();
        $payload['email'] = $user->getEmail();
        $payload['company'] = [
            'id' => $user->getCompany() ? $user->getCompany()->getId() : null,
            'name' => $user->getCompany() ? $user->getCompany()->getName() : null,
        ];

        $event->setData($payload);
    }
}

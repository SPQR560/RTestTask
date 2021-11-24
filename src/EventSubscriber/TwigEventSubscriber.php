<?php

namespace App\EventSubscriber;

use App\Entity\Profile;
use App\Repository\ProfileRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

class TwigEventSubscriber implements EventSubscriberInterface
{
    private Environment $twig;
    private ProfileRepository $profileRepository;

    public function __construct(Environment $twig, ProfileRepository $profileRepository)
    {
        $this->twig = $twig;
        $this->profileRepository = $profileRepository;
    }

    public function onControllerEvent(ControllerEvent $event)
    {
        $this->twig->addGlobal('profiles', $this->profileRepository->findAll());
    }

    public static function getSubscribedEvents()
    {
        return [
            ControllerEvent::class => 'onControllerEvent',
        ];
    }
}

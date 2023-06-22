<?php

declare(strict_types=1);

namespace App\Feature\SentryWebhook\EventListener;

use App\Feature\PlankaAuthenticator\Event\PlankaAuthenticatedEvent;
use App\Feature\SentryWebhook\Service\CreateBugfixCardService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
final class PlankaAuthenticatedEventListener
{
    public function __construct(private readonly CreateBugfixCardService $service)
    {
    }

    public function __invoke(PlankaAuthenticatedEvent $event): void
    {
        $this->service->handle($event->getIntegrator());
    }
}
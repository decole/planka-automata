<?php

declare(strict_types=1);

namespace App\Feature\PlankaAuthenticator\Service;

use App\Feature\ApiIntegration\Service\PlankaApiIntegrator;
use App\Feature\PlankaAuthenticator\Event\PlankaAuthenticatedEvent;
use Planka\Bridge\Exceptions\AuthenticateException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class PlankaAuthenticateEventService
{
    public function __construct(
        private readonly PlankaApiIntegrator $integrator,
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    /**
     * @throws AuthenticateException
     */
    public function execute(): void
    {
        $this->integrator->authenticate();

        $this->dispatcher->dispatch(new PlankaAuthenticatedEvent($this->integrator));
    }
}
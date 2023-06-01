<?php

declare(strict_types=1);

namespace App\Feature\PlankaAuthenticator\Event;

use App\Feature\ApiIntegration\Service\PlankaApiIntegrator;
use Symfony\Contracts\EventDispatcher\Event;

final class PlankaAuthenticatedEvent extends Event
{
    public function __construct(private readonly PlankaApiIntegrator $integrator)
    {
    }

    public function getIntegrator(): PlankaApiIntegrator
    {
        return $this->integrator;
    }
}
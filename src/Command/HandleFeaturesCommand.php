<?php

declare(strict_types=1);

namespace App\Command;

use App\Feature\PlankaAuthenticator\Service\PlankaAuthenticateEventService;
use Planka\Bridge\Exceptions\AuthenticateException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'cli:planka', description: 'cli command as entrypoint interaction with Planka')]
final class HandleFeaturesCommand extends Command
{
    public function __construct(private readonly PlankaAuthenticateEventService $service)
    {
        parent::__construct();
    }

    /**
     * @throws AuthenticateException
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->service->execute();

        return self::SUCCESS;
    }
}
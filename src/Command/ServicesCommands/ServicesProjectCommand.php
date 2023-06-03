<?php

declare(strict_types=1);

namespace App\Command\ServicesCommands;

use App\Feature\ApiIntegration\Service\PlankaApiIntegrator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'cli:projects', description: 'cli command see Planka projects')]
final class ServicesProjectCommand extends Command
{
    public function __construct(private readonly PlankaApiIntegrator $integrator)
    {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->integrator->authenticate();

        $client = $this->integrator->getClient();

        $dto = $client->project->list();

        $output->writeln('Project list:');

        foreach ($dto->items as $project) {
            $output->writeln("Id: $project->id | Name: $project->name");
        }

        return self::SUCCESS;
    }
}
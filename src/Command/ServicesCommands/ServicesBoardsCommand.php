<?php

declare(strict_types=1);

namespace App\Command\ServicesCommands;

use App\Feature\ApiIntegration\Service\PlankaApiIntegrator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'cli:boards', description: 'cli command see Planka boards')]
final class ServicesBoardsCommand extends Command
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

        $output->writeln('Board list:');

        foreach ($dto->included->boards as $board) {
            $output->writeln("Id: $board->id | Name: $board->name");
        }

        return self::SUCCESS;
    }
}
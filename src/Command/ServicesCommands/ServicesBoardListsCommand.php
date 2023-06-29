<?php

declare(strict_types=1);

namespace App\Command\ServicesCommands;

use App\Feature\ApiIntegration\Service\PlankaApiIntegrator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'cli:board-lists', description: 'cli command see Planka board lists')]
final class ServicesBoardListsCommand extends Command
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
            $output->writeln("Board id: $board->id | Name: $board->name" . PHP_EOL);

            $boardDto = $client->board->get($board->id);

            foreach ($boardDto->included->lists as $list) {
                $output->writeln("List id: $list->id | Name: $list->name");
            }

            $output->writeln(PHP_EOL . '----' . PHP_EOL);
        }

        return self::SUCCESS;
    }
}
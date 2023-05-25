<?php

declare(strict_types=1);

namespace App\Command;

use App\Module\AutoNumerator\Service\AutoNumerateCardService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'cli:auto-numerate', description: 'Auto numerate cli command Planka cards with special label')]
class AutoNumeratorCommand extends Command
{
    public function __construct(private readonly AutoNumerateCardService $service)
    {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->service->handle();

        return self::SUCCESS;
    }
}
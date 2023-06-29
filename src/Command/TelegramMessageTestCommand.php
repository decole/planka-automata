<?php

declare(strict_types=1);

namespace App\Command;

use App\Feature\TelegramNotify\Service\TelegramNotifyService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'cli:telegram', description: 'cli command test Telegram message')]
final class TelegramMessageTestCommand extends Command
{
    public function __construct(private readonly TelegramNotifyService $service)
    {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $question = new Question('Set telegram user id?', false);

        $id = $helper->ask($input, $output, $question);

        if (!$id) {
            $output->writeln('Input telegram user id!');
            return self::FAILURE;
        }

        $this->service->send((int)$id, 'Your utf8 text 😜 ...');

        return self::SUCCESS;
    }
}
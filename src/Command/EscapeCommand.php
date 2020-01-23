<?php

namespace App\Command;

use App\Service\EscapingService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class EscapeCommand extends Command
{
    protected static $defaultName = 'app:escape';
    private $escapeService;

    public function __construct(EscapingService $storageService,$name = null)
    {
        parent::__construct($name);
        $this->escapeService = $storageService;
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            //->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');


        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        $escapedArgument = $this->escapeService->escape($arg1, "-");

        $io->success("Result is ". $escapedArgument);

        return 0;
    }
}

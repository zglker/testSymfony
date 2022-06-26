<?php namespace Console;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Console\Command;

class ProcessCommand extends Command
{
    public function configure()
    {
        $this->setName('test')
            ->setDescription('Create summary data for remote orders')
            ->setHelp('This command is to create summary data for remote orders with parameters: email(who will receive the output)')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the receiver.')
            ->addArgument('format', InputArgument::REQUIRED, 'Format of the file(csv,jsonl)');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->test($input, $output);
        return 0;
    }
}
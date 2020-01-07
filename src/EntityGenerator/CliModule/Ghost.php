<?php namespace Zenit\Bundle\Ghost\EntityGenerator\CliModule;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Zenit\Bundle\Ghost\EntityGenerator\Component\EntityGenerator;

class Ghost extends Command {

	/** @var SymfonyStyle */
	protected $output;

	protected function configure() {
		$this
			->setName('ghost')
			->setDescription('Creates ghost entities')
			->addArgument('name', InputArgument::OPTIONAL)
			->addArgument('table', InputArgument::OPTIONAL)
			->addArgument('database', InputArgument::OPTIONAL)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) { EntityGenerator::Service()->execute($input, $output, $this->getApplication()); }

}

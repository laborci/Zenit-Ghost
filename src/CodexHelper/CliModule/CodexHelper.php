<?php namespace Zenit\Bundle\Ghost\CodexHelper\CliModule;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Zenit\Bundle\Ghost\CodexHelper\Component\CodexHelperGenerator;
use Zenit\Bundle\Mission\Component\Cli\CliModule;

class CodexHelper extends CliModule {

	/** @var SymfonyStyle */
	protected $output;

	protected function configure() {
		$this
			->setName('codexhelper')
			->setAliases(['ch'])
			->setDescription('Creates ghost entities')
			->addArgument('name', InputArgument::OPTIONAL)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) 	{
		CodexHelperGenerator::Service()->execute($input, $output, $this->getApplication());
	}

}

<?php namespace Zenit\Bundle\Ghost\CodexHelper\Component;

use CaseHelper\CaseHelperFactory;
use Minime\Annotations\Reader;
use Zenit\Bundle\DBAccess\Component\ConnectionFactory;
use Zenit\Bundle\DBAccess\Component\PDOConnection\AbstractPDOConnection;
use Zenit\Bundle\Ghost\Entity\Component\Model;
use Zenit\Bundle\Ghost\Entity\Component\Relation;
use Zenit\Bundle\Ghost\CodexHelper\Config;
use Zenit\Core\Code\Component\CodeFinder;
use Zenit\Core\ServiceManager\Component\Service;
use Zenit\Core\ServiceManager\Component\ServiceContainer;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CodexHelperGenerator{

	use Service;
	protected $ghostPath;
	protected $ghostNamespace;

	/** @var SymfonyStyle */
	protected $style;
	/** @var InputInterface */
	protected $input;
	/** @var OutputInterface */
	protected $output;
	/** @var Application */
	protected $application;
	/** @var array|mixed */
	protected $ghosts;
	protected $codexHelperNamespace;

	public function __construct(){
		$config = Config::Service();
		$this->ghostPath = $config->path;
		$this->ghostNamespace = $config->namespace;
		$this->ghosts = $config->ghosts;
		$this->defaultDatabase = $config->defaultDatabase;
		$this->codexHelperNamespace = $config->codexHelperNamespace;
	}

	public function execute(InputInterface $input, OutputInterface $output, Application $application){

		foreach ($this->ghosts as $ghost => $properties){

			$class = $this->codexHelperNamespace . '\\' . $ghost . 'GhostCodexHelper';
			$ghostClass = $this->ghostNamespace . '\\' . $ghost;
			/** @var Model $model */
			$model = $ghostClass::$model;

			// READ EXISTING ANNOTATIONS as TRANSLATIONS
			$translations = new Translation();
			if (class_exists($class)){
				/** @var \Minime\Annotations\Reader $reader */
				$reader = ServiceContainer::get(Reader::class);
				$translations->addFromAnnotations($reader->getClassAnnotations($class)->getAsArray('label-field'));
				$translations->addFromAnnotations($reader->getClassAnnotations($class)->getAsArray('label-attachment'));
			}

			/** @var Field[] $fields */
			$fields = [];
			foreach ($model->fields as $field) $fields[] = new Field('field', $field->name, $field->options, $translations);
			foreach ($model->getAttachmentStorage()->getCategories() as $category) $fields[] = new Field('attachment', $category->getName(), [], $translations);

			$fieldCollection = [];
			$fieldConstructorCollection = [];
			$annotationCollection = [];
			foreach ($fields as $field){
				$annotationCollection = array_merge($annotationCollection, $field->getTranslateAnnotations());
				$fieldCollection[] = $field->getField();
				$fieldConstructorCollection[] = $field->getFieldConstructor();
			}

			$template = file_get_contents(__DIR__ . '/../Resource/helper.txt');

			$template = str_replace('{{name}}', $ghost, $template);
			$template = str_replace('{{namespace}}', $this->codexHelperNamespace, $template);
			$template = str_replace('{{ghostNamespace}}', $this->ghostNamespace, $template);
			$template = str_replace('{{fields}}', join("\n", $fieldCollection), $template);
			$template = str_replace('{{fieldConstructors}}', join("\n", $fieldConstructorCollection), $template);
			$template = str_replace('{{annotations}}', join("\n", $annotationCollection), $template);

			$filename = CodeFinder::Service()->Psr4ResolveClass($this->codexHelperNamespace . '\\' . $ghost . 'GhostCodexHelper');
			file_put_contents($filename, $template);

			$output->writeln(realpath($filename).' done.');
		}
	}
}




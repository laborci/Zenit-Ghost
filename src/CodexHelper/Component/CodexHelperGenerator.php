<?php namespace Zenit\Bundle\Ghost\CodexHelper\Component;

use CaseHelper\CaseHelperFactory;
use Minime\Annotations\Reader;
use Zenit\Bundle\DBAccess\Component\ConnectionFactory;
use Zenit\Bundle\DBAccess\Component\PDOConnection\AbstractPDOConnection;
use Zenit\Bundle\Ghost\Entity\Component\Field;
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
			$fields = [];
			$fieldConstructors = [];
			$labels = [];
			$annotations = [];

			$class = $this->codexHelperNamespace . '\\' . $ghost . 'GhostCodexHelper';

			$translations = [];

			if (class_exists($class)){
				/** @var \Minime\Annotations\Reader $reader */
				$reader = ServiceContainer::get(Reader::class);
				$translations = $reader->getClassAnnotations($class)->getAsArray('label-field');
				$translations_avatar = $reader->getClassAnnotations($class)->getAsArray('label-attachment');
			}
			$translations = array_column(array_map(function ($value){
				[$key, $value] = explode(':', $value, 2);
				return [trim($key), trim($value)];
			}, $translations), 1, 0);
			$translations_avatar = array_column(array_map(function ($value){
				[$key, $value] = explode(':', $value, 2);
				return [trim($key), trim($value)];
			}, $translations_avatar), 1, 0);


			$ghostClass = $this->ghostNamespace . '\\' . $ghost;
			/** @var Model $model */
			$model = $ghostClass::$model;
			foreach ($model->fields as $field){
				$labels[] = $field->name;
				$fields[] = "\t/** @var \Zenit\Bundle\Codex\Component\Codex\Field */ protected $" . $field->name . ";";

				$opts = null;

				if(is_array($field->options)){
					$opts = array_column(array_map(function ($value) use ($translations, $field){
						return [$value, array_key_exists($field->name.'.'.$value, $translations) ? $translations[$field->name.'.'.$value] : $value];
					}, $field->options), 1, 0);
				}

				$fieldConstructors[] = "\t\t\$this->" . $field->name . " = new \Zenit\Bundle\Codex\Component\Codex\Field('" . $field->name . "', '"
					.(array_key_exists($field->name, $translations) && $translations[$field->name]  ? $translations[$field->name] : $field->name)."' "
					. (is_array($opts) ? ',[' . join(', ', array_map(function ($key, $value){ return "'".$key."'=>'".($value?:$key)."'"; }, array_keys($opts), $opts)) .']' : "") . ");";

				if (is_array($field->options)) foreach ($field->options as $option) $labels[] = $field->name . '.' . $option;
			}



			foreach ($model->getAttachmentStorage()->getCategories() as $category){
				$labels_attachment[] = $category->getName();
				$fields[] = "\t/** @var \Zenit\Bundle\Codex\Component\Codex\Field */ protected $" . $category->getName() . ";";
					$fieldConstructors[] = "\t\t\$this->" . $category->getName() . " = new \Zenit\Bundle\Codex\Component\Codex\Field('" . $category->getName() . "', '"
					.(array_key_exists($category->getName(), $translations_avatar) && $translations_avatar[$category->getName()]  ? $translations_avatar[$category->getName()] : $category->getName())."' );";
			}



			foreach ($labels as $label){
				$annotations[] = " * @label-field " . $label . ": " . (array_key_exists($label, $translations) ? $translations[$label] : '');
			}
			foreach ($labels_attachment as $label){
				$annotations[] = " * @label-attachment " . $label . ": " . (array_key_exists($label, $translations_avatar) ? $translations_avatar[$label] : '');
			}

			$template = file_get_contents(__DIR__ . '/../Resource/helper.txt');

			$template = str_replace('{{name}}', $ghost, $template);
			$template = str_replace('{{namespace}}', $this->codexHelperNamespace, $template);
			$template = str_replace('{{ghostNamespace}}', $this->ghostNamespace, $template);
			$template = str_replace('{{fields}}', join("\n", $fields), $template);
			$template = str_replace('{{fieldConstructors}}', join("\n", $fieldConstructors), $template);
			$template = str_replace('{{annotations}}', join("\n", $annotations), $template);

			$filename = CodeFinder::Service()->Psr4ResolveClass($this->codexHelperNamespace . '\\' . $ghost . 'GhostCodexHelper');
			file_put_contents($filename, $template);
		}

	}

}
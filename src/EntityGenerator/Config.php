<?php namespace Zenit\Bundle\Ghost\EntityGenerator;
use Zenit\Core\Code\Component\CodeFinder;
use Zenit\Core\Env\Component\ConfigReader;
class Config extends ConfigReader{

	public $defaultDatabase = 'bundle.ghost.entity.default-database';
	/** @var array */
	public $ghosts = 'bundle.ghost.entity.entities';
	public $path = '';
	public $namespace = 'bundle.ghost.entity.namespace';

	public function __construct(){
		parent::__construct();
		$this->path = CodeFinder::Service()->Psr4ResolveNamespace($this->namespace);
	}
}
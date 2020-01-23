<?php namespace Zenit\Bundle\Ghost\EntityGenerator;
use Zenit\Core\Env\Component\ConfigReader;
class Config extends ConfigReader{

	public $defaultDatabase = 'bundle.ghost.entity.default-database';
	/** @var array */
	public $ghosts = 'bundle.ghost.entity.entities';
	public $path = 'bundle.ghost.entity.path';
	public $namespace = 'bundle.ghost.entity.namespace';
}
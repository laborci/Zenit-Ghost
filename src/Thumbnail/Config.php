<?php

namespace Zenit\Bundle\Ghost\Thumbnail;

use Zenit\Core\Env\Component\ConfigReader;
class Config extends ConfigReader{
	public $url = 'bundle.ghost.thumbnail.url';
	public $path = 'bundle.ghost.thumbnail.path';
	public $sourcePath = 'bundle.ghost.thumbnail.source-path';
	public $secret = 'bundle.ghost.thumbnail.secret';
}
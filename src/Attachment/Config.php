<?php namespace Zenit\Bundle\Ghost\Attachment;

use Zenit\Core\Env\Component\ConfigReader;

class Config extends ConfigReader{
	public $attachmentPath = "bundle.ghost.attachment.path";
	public $attachmentUrl = "bundle.ghost.attachment.url";
	public $attachmentMetaDBPath = "bundle.ghost.attachment.meta-db-path";
}
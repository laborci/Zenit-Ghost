<?php namespace Zenit\Bundle\Ghost\Attachment\Interfaces;

interface AttachmentOwnerInterface{
	public function getPath();
	public function onAttachmentAdded($data);
	public function onAttachmentRemoved($data);

}
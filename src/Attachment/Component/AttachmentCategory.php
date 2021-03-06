<?php namespace Zenit\Bundle\Ghost\Attachment\Component;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zenit\Bundle\Ghost\Attachment\Interfaces\AttachmentOwnerInterface;

class AttachmentCategory{

	protected $name;
	protected $acceptedExtensions = [];
	protected $maxFileSize = INF;
	protected $maxFileCount = INF;
	/** @var \Zenit\Bundle\Ghost\Attachment\Component\AttachmentStorage */
	private $attachmentStorage;
	/** @var \Zenit\Bundle\Ghost\Attachment\Component\AttachmentCategoryManager[] */
	private $attachmentCategoryManagers = [];

	function __construct($name, AttachmentStorage $attachmentStorage){
		$this->name = $name;
		$this->attachmentStorage = $attachmentStorage;
	}

	public function acceptExtensions(...$extensions): self{
		$this->acceptedExtensions = array_map(function ($ext){ return strtolower($ext); }, $extensions);
		return $this;
	}

	public function maxFileSize(int $maxFileSizeInBytes): self{
		$this->maxFileSize = $maxFileSizeInBytes;
		return $this;
	}

	public function maxFileCount(int $maxFileCount): self{
		$this->maxFileCount = $maxFileCount;
		return $this;
	}

	public function getCategoryManager(AttachmentOwnerInterface $owner): AttachmentCategoryManager{
		if (!array_key_exists($owner->getPath(), $this->attachmentCategoryManagers)) $this->attachmentCategoryManagers[$owner->getPath()] = new AttachmentCategoryManager($this, $owner);
		return $this->attachmentCategoryManagers[$owner->getPath()];
	}

	/** @return string[] */
	public function getAcceptedExtensions(){ return $this->acceptedExtensions; }

	/** @return int */
	public function getMaxFileSize(){ return $this->maxFileSize; }

	/** @return string */
	public function getName(): string{ return $this->name; }

	/** @return int */
	public function getMaxFileCount(){ return $this->maxFileCount; }

	public function getAttachmentStorage(): AttachmentStorage{ return $this->attachmentStorage; }

	public function isValidUpload(File $upload){
		if ($upload->getSize() > $this->maxFileSize)
			return false;
		$ext = $upload instanceof UploadedFile ? $upload->getClientOriginalExtension() : $upload->getExtension();
		if (!is_null($this->acceptedExtensions) && !in_array($ext, $this->acceptedExtensions))
			return false;
		return true;
	}

}
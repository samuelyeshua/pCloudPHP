<?php
namespace pCloudPHP\Resources;

use pCloudPHP\pCloudPHP;
use pCloudPHP\Resources\File;
use pCloudPHP\Interfaces\FolderInterface;
use pCloudPHP\Exceptions\OperationNotSupported;
use pCloudPHP\Exceptions\RequestError;
use pCloudPHP\Exceptions\OperationError;

class Folder extends pCloudPHP implements  FolderInterface 
{

	public $path;
	public $name;
	public $thumb = false;
	public $folderid;
	public $isshared = false;
	public $modified;
	public $created;
	public $comments;
	public $parentfolderid;

	protected $contents = [];

	public function __construct ( Array $populate = [] ) {
		parent::__construct();

		if (!empty($populate) and is_array($populate)) {
			foreach ($populate as $prop => $value) {

				if (property_exists($this, $prop))
					$this->{$prop} = $value;
			}
		}
	}

	public function create ( String $path ){
		try {
			$jsonContent 	= $this->makeApiCall('createfolder', compact('path'));
			$arrayContent 	= json_decode($jsonContent, true);

			return new Folder($arrayContent['metadata']);
		} catch (Exception $e) {
			echo $e;
		}
	}

	public function listContent ( Int $folderid ){
		try {
			$jsonContent 	= $this->makeApiCall('listfolder', compact('folderid'));
			$arrayContent 	= json_decode($jsonContent, true);

			return new Folder($arrayContent['metadata']);
		} catch (Exception $e) {
			echo $e;
		}
	}

	public function delete ( Int $folderid, Bool $recursive = false ) {
		try {
			if ($recursive == true)
				$this->makeApiCall('deletefolder', compact('folderid'));
			else
				$this->makeApiCall('deletefolder', compact('folderid'));

			return true;
		} catch (Exception $e) {
			echo $e;
		}
	}

	public function rename ( Int $folderid, String $toname ) {
		$jsonContent 	= $this->makeApiCall('renamefolder', compact(['toname','folderid']));
		$arrayContent	= json_decode($jsonContent, true);

		return new Folder($arrayContent['metadata']);
	}

	public function move ( Int $folderid, String $topath ) {
		$jsonContent = $this->makeApiCall('renamefolder', compact('folderid','topath'));
		$arrayContent = json_decode($jsonContent,true);

		return new Folder($arrayContent['metadata']);
	}

	public function getContents () {
		$contents = [];

		if (!empty( $this->contents ))
			foreach ($this->contents as $item)
				if ($item['isfolder'])
					$contents[] = new Folder($item);
				else
					$contents[] = new File($item);

		return $contents;
	}
}
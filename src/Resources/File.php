<?php

namespace pCloudPHP\Resources;

use pCloudPHP\pCloudPHP;
use pCloudPHP\Interfaces\FileInterface;
use pCloudPHP\Exceptions\OperationNotSupported;
use pCloudPHP\Exceptions\RequestError;
use pCloudPHP\Exceptions\OperationError;
use pCloudPHP\Exceptions\ArgumentInvalid;

class File extends pCloudPHP implements FileInterface 
{
	const TYPE_UNKNOWN	= 0;
	const TYPE_IMAGE 	= 1;
	const TYPE_VIDEO 	= 2;
	const TYPE_AUDIO 	= 3;
	const TYPE_DOCUMENT = 4;
	const TYPE_ARCHIVE 	= 5;

	public $id;
	public $name;
	public $hash;
	public $ismine;
	public $isshared;
	public $parentfolderid;
	public $category;
	public $path;
	public $contenttype;
	public $isdeleted;
	public $thumb;
	public $created;
	public $fileid;
	public $modified;

	public function __construct ( Array $populate = [] ) {
		parent::__construct();

		if (!empty($populate) and is_array($populate)) {
			foreach ($populate as $prop => $value) {

				if (property_exists($this, $prop))
					$this->{$prop} = $value;
			}
		}
	}

	/**
	 * Get file type
	 * @return String
	 */
	public function getType () {
		switch ($this->category) {
			case 1:
				return 'image';
				break;

			case 2:
				return 'video';
				break;

			case 3:
				return 'audio';
				break;

			case 4:
				return 'document';
				break;

			case 5:
				return 'archive';
				break;
			
			default:
				return 'uncategorized';
				break;
		}
	}

	/**
	 * Return the timestamps for the current file
	 * @return Array
	 */
	public function getTimestamps () {
		return [
			'created' => $this->created,
			'modified' => $this->modified
		];
	}

	public function upload ( String $file, String $filename, int $folderid ) {
		// TODO: Implements UploadFiles
	}

	public function uploadProgress(){}

	/**
	 * Download one file with id and insert to de the destination folder
	 * @param String $destination 
	 * @param String|null $fileid 
	 * @return boolean
	 */
	public function download ( String $destination, String $fileid = null ) {
		if (!$fileid)
			$fileid = $this->fileid;

		if (!is_numeric($fileid)) 
			throw new ArgumentInvalid("FileID is a invalid argument, recheck");

		if (empty($destination) || !is_string($destination))
			throw new ArgumentInvalid("Destination folder is not a valid argument");
			
		if (!is_dir($destination))
			if (!mkdir($destination,0777))
				throw new ArgumentInvalid("{$destination} do not exists");

		$fileLink 		= $this->getLink($fileid);
		$fileLinkFrag 	= explode('/', $fileLink);
		$fileName 		= array_pop($fileLinkFrag);
		$fileContent 	= file_get_contents($fileLink);
		$destination 	= $destination . $fileName;

		if (!file_put_contents($destination, $fileContent)) 
			return false;

		return true;
	}

	/**
	 * Return a direct link to access file on the cloud
	 * @param String $fileid 
	 * @return String
	 */
	public function getLink ( String $fileid ) {
		$jsonResponse = json_decode($this->makeApiCall('getfilelink', compact('fileid')));
		return "https://" . $jsonResponse->hosts[0] . $jsonResponse->path;
	}

	public function copy(){}

	/**
	 * Return a array with two hashs
	 * @param String $fileid 
	 * @return Array
	 */
	public function checksum ( String $fileid ) {
		if (!is_numeric($fileid))
			throw new ArgumentInvalid("FileID not valid");
			
		$jsonResponse = $this->makeApiCall('checksumfile', compact('fileid'));
		$objResponse = json_decode($jsonResponse);

		return [
			'sha1' 	=> $objResponse->sha1,
			'md5'	=> $objResponse->md5
		];
	}

	/**
	 * Move file to trash an return a instance of file deleted
	 * @param String $fileid 
	 * @return File
	 */
	public function delete ( String $fileid ) {
		if (!is_numeric($fileid))
			throw new ArgumentInvalid("FileID not valid");
			
		$jsonResponde	= $this->makeApiCall('deletefile', compact('fileid'));
		$arrayResponse	= json_decode($jsonResponse, true);

		return new File($arrayResponse['metadata']);
	}

	/**
	 * Rename file to another name and extension
	 * @param String $fileid 
	 * @param String $toname 
	 * @return File
	 */
	public function rename ( String $fileid, String $toname ) {
		if (!is_numeric($fileid))
			throw new ArgumentInvalid("FileID not valid");
			
		$jsonResponse 	= $this->makeApiCall('renamefile', compact('fileid','toname'));
		$arrayResponse	= json_decode($jsonResponse, true);

		return new File($arrayResponse['metadata']);
	}

	/**
	 * Move file to another folder
	 * @param String $fileid 
	 * @param String $tofolderid 
	 * @return File
	 */
	public function move( String $fileid, String $tofolderid ) {
		if (!is_numeric($fileid) || !is_numeric($tofolderid))
			throw new ArgumentInvalid("Invalid arguments");
			
		$jsonResponse	= $this->makeApiCall('renamefile', compact('fileid','tofolderid'));
		$arrayResponse	= json_decode($jsonResponse, true);

		return new File($arrayResponse['metadata']);
	}
}
<?php 
namespace pCloudPHP\Resources;

use pCloudPHP\pCloudPHP;
use pCloudPHP\Interfaces\StreamingInterface;
use pCloudPHP\Exceptions\OperationNotSupported;
use pCloudPHP\Exceptions\RequestError;
use pCloudPHP\Exceptions\OperationError;
use pCloudPHP\Exceptions\ArgumentInvalid;

class Streaming extends pCloudPHP implements StreamingInterface
{
	public $hosts;
	public $path;
	public $expires;
	public $variants;
	private static $rules = [
		'root' => [
			'forcedownload' 	=> ['_type' => 'boolean'],
			'contenttype' 		=> ['_type' => 'string'],
			'maxspeed' 			=> ['_type' => 'number'],
			'skipfilename' 		=> ['_type' => 'number'],
			'abitrate'			=> ['_type' => 'number'],
			'vbitrate'			=> ['_type' => 'number'],
			'resolution'		=> ['_type' => 'string'],
			'fixedbitrate'		=> ['_type' => 'boolean'],
			'fromencoding'		=> ['_type' => 'string'],
			'toencoding'		=> ['_type' => 'string'],
		]
	];

	public function __construct ( Array $populate = [] ) {
		parent::__construct(self::$rules);

		if (!empty($populate) and is_array($populate)) {
			foreach ($populate as $prop => $value) {

				if (property_exists($this, $prop))
					$this->{$prop} = $value;
			}
		}
	}

	public function getFileLink ( String $fileid, Array $options = [] ) {
		if (!is_numeric($fileid))
			throw new ArgumentInvalid("FileID invalid");

		if (!empty($options) && !$this->validator->validate($options))
			throw new ArgumentInvalid("Options not valid");
			
	
		$jsonResponse  = $this->makeApiCall(
			'getfilelink', 
			array_merge( compact('fileid'),$options )
		);
		$arrayResponse = json_decode($jsonResponse,true);

		return new Streaming($arrayResponse); 		
	}

	public function getVideoLink( String $fileid, Array $options = [], Bool $variants = false ){
		if (!is_numeric($fileid))
			throw new ArgumentInvalid("FileID invalid");

		if (!empty($options) && !$this->validator->validate($options))
			throw new ArgumentInvalid("Options not valid");
		
		$jsonResponse  = $this->makeApiCall(
			($variants)? 'getvideolinks' : 'getvideolink', 
			array_merge( compact('fileid'),$options )
		);
		$arrayResponse = json_decode($jsonResponse,true);

		return new Streaming($arrayResponse); 
	}

	public function getAudioLink( String $fileid, Array $options = [] ){
		if (!is_numeric($fileid))
			throw new ArgumentInvalid("FileID invalid");

		if (!empty($options) && !$this->validator->validate($options))
			throw new ArgumentInvalid("Options not valid");
		
		$jsonResponse  = $this->makeApiCall(
			'getaudiolink',
			array_merge( compact('fileid'),$options )
		);
		$arrayResponse = json_decode($jsonResponse,true);

		return new Streaming($arrayResponse);
	}

	public function getHlsLink( String $fileid, Array $options = [] ){
		if (!is_numeric($fileid))
			throw new ArgumentInvalid("FileID invalid");

		if (!empty($options) && !$this->validator->validate($options))
			throw new ArgumentInvalid("Options not valid");
		
		$jsonResponse  = $this->makeApiCall(
			'gethlslink',
			array_merge( compact('fileid'),$options )
		);
		$arrayResponse = json_decode($jsonResponse,true);

		return new Streaming($arrayResponse); 
	}

	public function getTextFile( String $fileid, Array $options = [] ){
		if (!is_numeric($fileid))
			throw new ArgumentInvalid("FileID invalid");

		if (!empty($options) && !$this->validator->validate($options))
			throw new ArgumentInvalid("Options not valid");
		
		$jsonResponse  = $this->makeApiCall(
			'gettextfile',
			array_merge( compact('fileid'),$options )
		);

		return $jsonResponse;//new Streaming($arrayResponse); 
	}
}
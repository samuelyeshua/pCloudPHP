<?php
namespace pCloudPHP;

use Requests;
use Volan\Volan;

use pCloudPHP\Exceptions\OperationNotSupported;
use pCloudPHP\Exceptions\RequestError;
use pCloudPHP\Exceptions\OperationError;


class pCloudPHP
{
	/**
	 * @var String
	 */
	protected static $api = 'https://api.pcloud.com/';

	/**
	 * @var String
	 */
	protected $configPath = __DIR__ . '/config.json';

	/**
	 * @var Array
	 */
	protected $config = [];

	/**
	 * @var String
	 */
	protected $token;

	/**
	 * @var Array $operations
	 */
	public static $operations = [
		'createfolder',
		'listfolder',
		'renamefolder',
		'deletefolder',
		'deletefolderrecursive',
		'uploadfile',
		'uploadprogress',
		'downloadfile',
		'copyfile',
		'checksumfile',
		'deletefile',
		'renamefile',
		'getfilelink',
		'getvideolink',
		'getvideolinks',
		'gethlslink',
		'getaudiolink',
		'gettextfile'
	];

	protected $validator;

	/**
	 * @var Int
	 */
	public $root = 0;

	/**
	 * Create an instance
	 * @param Int $root 
	 * @return void
	 */
	public function __construct ( Array $schema = [], Int $root = 0 ) {
		$this->root 		= $root;
		$this->validator 	= new Volan($schema);
		$this->config 		= json_decode(file_get_contents($this->configPath), true);

		if (array_key_exists('token', $this->config))
			$this->token = $this->config['token'];
	}

	/**
	 * Return token to access api methods
	 * @return String
	 */
	public function getToken () {
		return $this->token;
	}

	/**
	 * Return configs
	 * @return Array
	 */
	public function getConfig () {
		return $this->config;
	}

	/**
	 * Prepare url to send api requests
	 * @param String $action 
	 * @param Array|array $query 
	 * @return String
	 */
	public function buildUrl ( String $action, Array $query = [], Bool $token = true) {
		if ($token and $this->token)
			$query['access_token'] = $this->token;

		$query = !empty($query)? '?'. http_build_query($query) : ''; 

		return $this::$api . $action . $query;
	}

	/**
	 * Making direct calls to api pCloud and return results or
	 * Throw custom exceptions
	 * @param String $action 
	 * @param Array $query 
	 * @param String|string $method 
	 * @param Array|array $header 
	 * @return Json
	 */
	public function makeApiCall ( 
		String $action, 
		Array $query, 
		String $method = 'get', 
		Array $header = [] ) {

		if (!in_array($action, $this::$operations))
			throw new OperationNotSupported("This \"{$action}\" is not supported");

		if (strtolower($method) == 'get') {
			$url = $this->buildUrl( $action, $query );
			$request = Requests::get($url, $header);
		}
		else {
			$url = $this->buildUrl( $action );
			$request = Requests::post($url, $header, $query);
		}

		if ($request->status_code != 200)
			throw new RequestError("Error Processing Request", $request->status_code);

		$result = json_decode($request->body);
		if ($result->result != '0')
			throw new OperationError($result->error, $result->result);
			
		return json_encode($result);	
	}
}
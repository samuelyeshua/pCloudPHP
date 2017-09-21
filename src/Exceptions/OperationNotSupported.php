<?php 

namespace pCloudPHP\Exceptions;

use Exception;

class OperationNotSupported extends Exception {
	public function __construct ( $message, $code = 10 ) {
		parent::__construct($message, $code);
	}

	public function __toString () {
		return __CLASS__ . "[{$this->code}]: {$this->message}";
	}
}
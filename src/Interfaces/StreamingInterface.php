<?php
namespace pCloudPHP\Interfaces;

interface StreamingInterface {
	public function getFileLink( String $fileid, Array $options );
	public function getVideoLink( String $fileid, Array $options, Bool $variants );
	public function getAudioLink( String $fileid, Array $options );
	public function getHlsLink( String $fileid, Array $options );
	public function getTextFile( String $fileid, Array $options );
}
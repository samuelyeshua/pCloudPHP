<?php
namespace pCloudPHP\Interfaces;

interface FileInterface {

	public function upload ( String $file, String $filename, int $folderid );
	public function uploadProgress();
	public function download( String $destination, String $fileid = null );
	public function copy();
	public function checksum( String $fileid );
	public function delete( String $fileid );
	public function rename( String $fileid, String $toname );
	public function getLink( String $fileid );
	public function move( String $fileid, String $tofolderid );
}
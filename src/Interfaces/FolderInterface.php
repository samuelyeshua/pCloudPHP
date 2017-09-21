<?php
namespace pCloudPHP\Interfaces;

interface FolderInterface {

	public function create ( String $path );
	public function listContent ( Int $folderid );
	public function delete ( Int $folderid, Bool $recursive );
	public function rename ( Int $folderid, String $toname );
	public function move ( Int $folderid, String $topath );

	public function getContents ();
}
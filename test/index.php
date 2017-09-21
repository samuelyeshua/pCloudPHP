<?php
namespace pCloudPHP\Test;

require __DIR__ . '/../vendor/autoload.php';

use pCloudPHP\Resources\Folder;
use pCloudPHP\Resources\File;
use pCloudPHP\Resources\Streaming;

$folder = new Folder();
$file   = new File();
$stream = new Streaming();

echo "<pre><code>";
print_r($stream->getTextFile(3556228068));
echo "</code></pre>";

// foreach ($folder->listContent(936720973)->getContents() as $file) {
// 	if ($file->download('./'))
// 		echo $file->name . '----- Downloaded;';
// }
?>
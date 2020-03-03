<?php
	header("Access-Control-Allow-Origin: *");
	header("Content-Type: application/json; charset=utf-8");

	include_once("config.php");

	$path = __DIR__.FILES_PATH;
	$dirs = array_filter(glob(ltrim(FILES_PATH, '/').'*'), 'is_dir');
	$days = 0;  
	$removed = 0;
	$scanned = 0;

	function delete_dir($src) { 
		$dir = opendir($src);
		while(false !== ( $file = readdir($dir)) ) { 
			if (( $file != '.' ) && ( $file != '..' )) { 
				if ( is_dir($src . '/' . $file) ) { 
					delete_dir($src . '/' . $file); 
				} 
				else { 
					unlink($src . '/' . $file); 
				} 
			} 
		} 
		closedir($dir); 
		rmdir($src);
	}
	foreach($dirs as $dir) {
		$scanned++;
		if (time()-filemtime($dir) > KEEP_DAYS * 24 * 3600) { // file older than X hours (3600 - one hour)
			$removed++;
			delete_dir(__DIR__.'/'.$dir);
		}
	}
	function GetDirectorySize($path){
		$bytestotal = 0;
		$path = realpath($path);
		if($path!==false && $path!='' && file_exists($path)){
			foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object){
				$bytestotal += $object->getSize();
			}
		}
		return $bytestotal;
	}
	$size = GetDirectorySize($path);

	$output = array('scanned' => $scanned, 'removed' => $removed, 'space_used' => (int)($size / 1024 / 1024) . 'Mb');
	die(json_encode($output));
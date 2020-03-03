<?php
	error_reporting(E_ERROR | E_PARSE);
	$pack = isset($_GET['p']) ? $_GET['p'] : false;
	$file = isset($_GET['f']) ? $_GET['f'] : false;
	$zip  = isset($_GET['z']) ? $_GET['z'] : false;
	$dev  = isset($_GET['dev']) ? true : false;

	$PATH = __DIR__.'/server/files/'.$pack;
	$file_to_download = $PATH.'/'.$file;
	$ZIP = $pack.'.zip';
	$ZIP_PATH = $PATH.'/'.$ZIP;

	if($zip) {
		if(!file_exists($ZIP_PATH)) {
			$zip_shell_command = "cd $PATH; zip -rq -j $ZIP $PATH";
			$zip = exec( $zip_shell_command, $output, $return_var);
		}
		$file_to_download = $ZIP_PATH;
	} 
	if($dev) {
		echo '<pre>';
		echo $file;
		print_r($file_to_download);

	} else {
		header("Expires: 0");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");  header("Content-type: application/file");
		header('Content-length: '.filesize($file_to_download));
		header('Content-disposition: attachment; filename='.basename($file_to_download));
		readfile($file_to_download);
	}
	exit;

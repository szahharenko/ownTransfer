<?php
	function getListOfFiles($dir_path) {
		$resultArray = false;
		if(file_exists($dir_path)) {
			$open_dir = opendir($dir_path);
			while($entryName=readdir($open_dir)) {
				$resultArray[] = $entryName;
			}
			closedir($open_dir);
			$resultArray = sort_dir_files($dir_path);
		}
		return $resultArray;
	}
	function bytesFormat($size) {
		$size = (int) ($size / 1024);
		$unit = 'Kb';
		if($size > 1024) {
			$size =  number_format((float)($size / 1024), 2, '.', '');
			$unit = 'Mb';
		}
		if($size > 1024) {
			$size =  number_format((float)($size / 1024), 2, '.', '');
			$unit = 'Gb';
		}
		return $size.' '.$unit;
	}

	function GetDirectorySize($path){
		$bytestotal = 0;
		$path = realpath($path);
		if($path!==false && $path!='' && file_exists($path)){
			foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object){
				$bytestotal += $object->getSize();
			}
		}
		$total_size = bytesFormat($bytestotal);
		return $total_size;
	}
	function scan_dir($dir) {
		$ignored = array('.', '..', '.svn', '.htaccess', $pack.'zip');
		$files = array();    
		foreach (scandir($dir) as $file) {
			if (in_array($file, $ignored)) continue;
				$files[$file] = filemtime($dir . '/' . $file);
		}
		arsort($files);
		$files = array_keys($files);
		return ($files) ? $files : false;
	}
	function sort_dir_files($dir) {
		$sortedData = array();
		$files = array_reverse(scan_dir($dir));
		if ($files) {
			foreach($files as $file) {
				if(is_file($dir.'/'.$file))
					array_push($sortedData, $file);
				else
					array_unshift($sortedData, $file);
			}
			return $sortedData;
		} else {
			return false;
		}
	}
	function findexts($filename) {
		$filename=strtolower($filename);
		$exts = explode("[/\\.]", $filename);
		$n=count($exts)-1;
		$exts=$exts[$n];
		return $exts;
	}
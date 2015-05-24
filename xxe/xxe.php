<?php
	ini_set('display_errors',true);
	class FaultException extends Exception
	{
	
	}
	
	define("UPLOAD_DIR","upload/");

	function parseXml($xml){
		if($xml !== null){
			//libxml_disable_entity_loader(true);
 			$dom = new DOMDocument();
		
 			if ($dom->load($xml) !== FALSE){
				print $dom->saveXML();
			}else{
				throw new FaultException('Error parsing xml');
			}
		}
		else
			throw new FaultException('Error reading content.xml');
	}

	function getContentXml($filename){
		$filepath = UPLOAD_DIR .$filename;
		$filedirectory = $filepath . 'dir';
		mkdir($filedirectory);		
		$contentfile = $filedirectory .'/content.xml';
		$xml = null;
		$zip = new ZipArchive;
		if ($zip->open($filepath) === TRUE) {
    			$zip->extractTo($filedirectory);
    			$zip->close();
			$handle = fopen($contentfile,'r');		
			$xml =  fread($handle, filesize($contentfile));
			fclose($handle);
		} else {
			throw new FaultException('Error extracting file');
		}
		return $contentfile;
	 }

	function saveFile(){
		
		if (!empty($_FILES["file1"])) {
    		$myFile = $_FILES["file1"];
 
    	if ($myFile["error"] !== UPLOAD_ERR_OK) {
       	 	throw new FaultException("Error uploading.");
    	}	
    	// ensure a safe filename
    	$name = preg_replace("/[^A-Z0-9._-]/i", "_", $myFile["name"]);
 
    	// don't overwrite an existing file
    	$i = 0;
    	$parts = pathinfo($name);
    	while (file_exists(UPLOAD_DIR . $name)) {
       		 $i++;
      		 $name = $parts["filename"] . "-" . $i . "." . $parts["extension"];
    	}
 
    	if (!move_uploaded_file($myFile["tmp_name"], UPLOAD_DIR . $name)) { 
        	throw new FaultException("Error saveing.");
    	}
 
   		chmod(UPLOAD_DIR . $name, 0644);
   		return $name;
		}
	} 
	function showUpload(){
		require("upload.html");	
	}

	function showTable(){
	}
	if(isset($_POST['submit'])){
		showTable(parseXml(getContentXml(saveFile())));
	}
	else showUpload();


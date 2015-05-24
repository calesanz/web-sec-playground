<?php
	ini_set('display_errors',true);
	class FaultException extends Exception
	{
	
	}
	
	define("UPLOAD_DIR","upload/");

	function parseXml(){
		if($xml !== null){
//libxml_disable_entity_loader(true);
 		$dom = new DOMDocument();
		
 		if ($dom->load($xml) !== FALSE){

		}else{
			throw new FaultException('Error parsing xml');
		}
	}

	function getContentXml($filename){
		echo $filename;		$path = 'upload/'.$filename;
		mkdir($path . 'dir');		
		$xml = null;
		$zip = new ZipArchive;
		if ($zip->open($path) === TRUE) {
    		$zip->extractTo($path . 'dir');
    		$zip->close();
		$handle = fopen($path,'r');		
		$xml =  fread($handle, filesize($path . 'content.xml');
		fclose($handle);
		} else {
			throw new FaultException('Error extracting file');
		}
		return $xml;
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
		require("upload.hmtl");	
	}

	function showTable(){
	
	}
	if(isset($_POST['Submit'])){
		showTable(parseXml(getContentXml(saveFile())));
	}


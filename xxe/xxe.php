<?php

	class FaultException extends Exception
	{
	
	}
	
	define("UPLOAD_DIR","upload/");


	function getContentXml($file){
		//libxml_disable_entity_loader(true);
		print_r($file);
// 		$dom = new DOMDocument();
		
// 		if ($dom->load($xml) !== FALSE)
// 			echo "loaded remote xml!\n";
// 		else
// 			echo "failed to load remote xml!\n";
		
		$zip = new ZipArchive;
		if ($zip->open('test.zip') === TRUE) {
    		$zip->extractTo('/my/destination/dir/');
    		$zip->close();
    		echo 'ok';
		} else {
    		echo 'failed';
		}
	}
	function saveFile(){
		
		if (!empty($_FILES["file1"])) {
    		$myFile = $_FILES["file1"];
 
    	if ($myFile["error"] !== UPLOAD_ERR_OK) {
       	 	throw new FaultException("Error uploading.");
    	}	
 
 		if($myFile["type"]!=="ods"){
 			throw new FaultException("Wrong filetype.");
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

	if(isset($_POST['submit'])){
		showTable(parseXml(getContentXml(saveFile())));
	}


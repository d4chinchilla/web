<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

$target_dir = "uploads/";
$target_file = $target_dir . "firmware.zip";

$goodFile = 1;

$fileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_EXTENSION));

if($_FILES["fileToUpload"]["size"] > 1000000) {
	echo nl2br("File too large, must be <1MB. \n");
	$goodFile = 0;
}

if($fileType != "zip") {
	echo nl2br("Incorrect file type, please upload .zip only. \n" );
	$goodFile = 0;
}

if($goodFile == 0) {
	echo nl2br("File not uploaded. \n Redirecting...");
}else{
	if(move_uploaded_file($_FILES["fileToUpload"]["tmp_name"],$target_file)) {
		echo nl2br("The file " . basename($_FILES["fileToUpload"]["name"]). " has been uploaded. \n Redirecting...");
		$extFirmware = new ZipArchive;
		if($extFirmware->open('uploads/firmware.zip') == TRUE) {
			$extFirmware->extractTo('fwExtract/');
			$extFirmware->close();
			echo nl2br("Firmware successfully extracted.");
			$command = escapeshellcmd('fwExtract/installationScripts/install');
			$output = shell_exec($command);
			echo $output;
		} else {
			echo nl2br("Firmware extraction failed.");
		}	
	} else {
		echo nl2br("Sorry, error uploading file, please try again. \n Redirecting...");
	}
}
header('refresh:5; url=index.php');
die();
?>
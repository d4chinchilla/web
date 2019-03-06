<?php
//Display all warnings
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

//Target directory and file name for uploaded files
$target_dir = "uploads/";
$target_file = $target_dir . "firmware.zip.gpg";

//goodFile flag to check if file is good
$goodFile = 1;

//String variable of filetype for later checking
$fileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_EXTENSION));

//Prevent large file uploads
if($_FILES["fileToUpload"]["size"] > 1000000) {
	echo nl2br("File too large, must be <1MB. \n");
	$goodFile = 0;
}

//Check file type is type gpg, reject if not
if($fileType != "gpg") {
	echo nl2br("Incorrect file type, please upload signed .zip.gpg only. \n" );
	$goodFile = 0;
}
//Do not upload file if too large/not correct type
if($goodFile == 0) {
	echo nl2br("File not uploaded. \n Redirecting...");
}else{
    //Move uploaded file from secure temp to target directory
	if(move_uploaded_file($_FILES["fileToUpload"]["tmp_name"],$target_file)) {
		echo nl2br("The file " . basename($_FILES["fileToUpload"]["name"]). " has been uploaded. \n Redirecting...");
			//exec("fwExtract/installationScripts/install");
        //Command control file to handle file
        echo "Opening";
        $filePath = fopen("chinchilla-reset","w");
        echo $filePath;
        //if(!$filePath) {echo "File Open failed";}
        echo "Writing";
        fwrite($filePath,"install\n");
        echo "Closing";
        fclose($filePath);

	} else {
	    //If problem during upload, echo error
		echo nl2br("Sorry, error uploading file, please try again. \n Redirecting...");
	}
}
//After 5 seconds, redirect to index and kill process
header('refresh:5; url=index.php');
die();
?>
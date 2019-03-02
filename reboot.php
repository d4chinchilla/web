<?php
/**
 * Created by PhpStorm.
 * User: matty
 * Date: 02/03/2019
 * Time: 15:59
 */
echo "Opening";
$filePath = fopen("chinchilla-reset","w");
echo $filePath;
//if(!$filePath) {echo "File Open failed";}
echo "Writing";
fwrite($filePath,"reset\n");
echo "Closing";
fclose($filePath);
?>
<?
// CHECK LOGIN
$file_login_check = "../../login_check.php";

if (file_exists($file_login_check)) {
	include $file_login_check;
}
?>
<form action="includes/module_action.php" method="post" enctype="multipart/form-data">
	extensions: [doc, docm, xls, xlsm, pdf, hta, chm] <br><br>
	<input type="file" name="fileToUpload" id="fileToUpload">
	<br>
	<input type="submit" value="upload" name="submit">
	<input type="hidden" name="upload" value="upload">
</form>
<br>
<?php
/*
foreach (glob("includes/uploads/*") as $filename) {
	echo "$filename size " . filesize($filename) . "<br>";
}
*/

echo "<b>Uploaded Files:</b><br>";

//$files_path = "includes/FruityProxy-master/logs/Delivery/";
$files_path = "includes/FruityProxy-master/content/Delivery/";

if (!file_exists($files_path)) {
	$exec = "mkdir -p $files_path";
	exec_fruitywifi($exec);
}


$files = glob($files_path.'*');

//$exec = "ls " . $files_path;
//$files = exec_fruitywifi($exec);
//echo $files;

for ($i = 0; $i < count($files); $i++) {
	$filename = str_replace($files_path,"",$files[$i]);
	echo "- $filename<br>";
}

?>
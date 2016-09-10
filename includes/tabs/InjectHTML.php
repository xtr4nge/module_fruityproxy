<?
// CHECK LOGIN
$file_login_check = "../../../../login_check.php";

if (file_exists($file_login_check)) {
	include $file_login_check;
}
?>
<form id="formInject" name="formInject" method="POST" autocomplete="off" action="includes/save.php">
	<input type="submit" value="save">
	<br><br>
	<?
		$filename = "$mod_path/includes/FruityProxy-master/content/InjectHTML/inject.txt";
		
		/*
		if ( 0 < filesize( $filename ) ) {
			$fh = fopen($filename, "r"); // or die("Could not open file.");
			$data = fread($fh, filesize($filename)); // or die("Could not read file.");
			fclose($fh);
		}
		*/
		
		$data = open_file($filename);		
	?>
	<textarea id="inject" name="newdata" class="module-content" style="font-family: monospace, courier;"><?=htmlspecialchars($data)?></textarea>
	<input type="hidden" name="type" value="inject">
</form>
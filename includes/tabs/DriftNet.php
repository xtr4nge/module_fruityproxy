<?php
// CHECK LOGIN
$file_login_check = "../../login_check.php";

if (file_exists($file_login_check)) {
	include $file_login_check;
}

//REF: http://stackoverflow.com/questions/7121479/listing-all-the-folders-subfolders-and-files-in-a-directory-using-php

// Checking POST & GET variables...
if ($regex == 1) {
	//regex_standard($_GET["folderName"], "../../../../msg.php", $regex_extra);
    regex_standard($_POST["driftnet-foldername"], "../../msg.php", $regex_extra);
}

//$folderName = $_GET["folderName"];
$folder_selected = $_POST["driftnet-foldername"];
if ($folder_selected != "ALL") $folderName = $folder_selected;

$pathLen = 0;

function prePad($level) {
	$ss = "";
	
	for ($ii = 0;  $ii < $level;  $ii++) {
		$ss = $ss . "|&nbsp;&nbsp;  ";
	}
	
	return $ss;
}

function myScanDir($dir, $level, $rootLen) {
	global $pathLen;
	global $folderName;
  
	if ($handle = opendir($dir)) {
  
	  $allFiles = array();
  
	  while (false !== ($entry = readdir($handle))) {
		if ($entry != "." && $entry != "..") {
		  if (is_dir($dir . "/" . $entry))
		  {
			$allFiles[] = "D: " . $dir . "/" . $entry;
		  }
		  else
		  {
			$allFiles[] = "F: " . $dir . "/" . $entry;
		  }
		}
	  }
	  closedir($handle);
  
	  natsort($allFiles);
  
	  foreach($allFiles as $value)
	  {
		$displayName = substr($value, $rootLen + 4);
		$fileName    = substr($value, 3);
		$linkName    = str_replace(" ", "%20", substr($value, $pathLen + 3));
		if (is_dir($fileName)) {
		  echo prePad($level) . $linkName . "<br>\n";
		  myScanDir($fileName, $level + 1, strlen($fileName));
		} else {
		  //echo prePad($level) . "<a href=\"" . $linkName . "\" style=\"text-decoration:none;\">" . $displayName . "</a><br>\n";
		  //$show_link = "<a href='includes/FruityProxy-master/logs/DriftNet/$folderName/" . $linkName . "' style='text-decoration:n-one;'>" . $displayName . "</a><br>\n";
		  /*
		  $show_link = "<a href='includes/FruityProxy-master/logs/DriftNet/$folderName/" . $linkName . "' style='text-decoration:n-one;'>" . $displayName . "</a><br>\n";
		  $show_link = preg_replace('~//+~', '/', $show_link);
		  */
		  $img_path = "includes/FruityProxy-master/logs/DriftNet/$folderName/$linkName";
		  $img_path = preg_replace('~//+~', '/', $img_path);
		  $img_path = str_replace('includes/', '', $img_path);
		  $show_link = "<a href='#' onclick='PopupPic(\"$img_path\")' style='text-decoration:n-one;'>" . $displayName . "</a><br>\n";
		  echo prePad($level) . $show_link;
		  //echo prePad($level) . "<a href='includes/FruityProxy-master/logs/DriftNet/$folderName/" . $linkName . "' style='text-decoration:n-one;'>" . $displayName . "</a><br>\n";
		}
	  }
	}
}

?>
<?
$pathImages = "includes/FruityProxy-master/logs/DriftNet/";
$list = glob($pathImages.'*');

if ($folder_selected != "" and $folder_selected != "ALL") $folderName = $folder_selected;

?>
<form action="?tab=tab-DriftNet" method="POST">
	<select name="driftnet-foldername" onchange="this.form.submit()">
		<option>ALL</option>
		<?
		for ($i = 0; $i < count($list); $i++) {
			$folder = str_replace($pathImages,"",$list[$i]);
		
			//echo "<a href='list_files.php?folderName=".$folderName."'>$folderName</a> ";
			//echo "<br>";
			if ($folder == $folder_selected) $selected = "selected"; else $selected = "";
			echo "<option $selected>$folder</option>";
		}
		?>
	</select>
</form>
<br>

<div class='module-content' style="overflow:scroll; font-family: monospace, courier;">
	<p>
	<?php
		echo "[ $folderName ]<br>";
		$root = "includes/FruityProxy-master/logs/DriftNet/$folderName/";
	  
		$pathLen = strlen($root);
		  
		myScanDir($root, 0, strlen($root));
	?>
	</p>
</div>
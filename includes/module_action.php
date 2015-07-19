<? 
/*
	Copyright (C) 2013-2015 xtr4nge [_AT_] gmail.com

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/ 
?>
<?
include "../../../config/config.php";
include "../_info_.php";
include "../../../login_check.php";
include "../../../functions.php";

include "options_config.php";

// Checking POST & GET variables...
if ($regex == 1) {
    regex_standard($_GET["service"], "../msg.php", $regex_extra);
    regex_standard($_GET["file"], "../msg.php", $regex_extra);
    regex_standard($_GET["action"], "../msg.php", $regex_extra);
    regex_standard($_GET["install"], "../msg.php", $regex_extra);
	regex_standard($_GET["plugin"], "../msg.php", $regex_extra);
	regex_standard($_POST["upload"], "../msg.php", $regex_extra);
}

$service = $_GET['service'];
$action = $_GET['action'];
$page = $_GET['page'];
$install = $_GET['install'];
$plugin = $_GET['plugin'];
$upload = $_POST['upload'];

if($service != "") {
    if ($action == "start") {
        // COPY LOG
		$exec = "$bin_mv $mod_logs $mod_logs_history/".gmdate("Ymd-H-i-s").".log";
		exec_fruitywifi($exec);
        
		if ($us_mode == "-") {
		
			$exec = "$bin_iptables -t nat -A PREROUTING -p tcp --destination-port 80 -j REDIRECT --to-port 8080";
			exec_fruitywifi($exec);
			
			//$exec = "$bin_iptables -t nat -A PREROUTING -p tcp --destination-port 443 -j REDIRECT --to-port 10000";
			//exec_fruitywifi($exec);
			
			//$exec = "$bin_iptables -t nat -A PREROUTING -p tcp --destination-port 53 -j REDIRECT --to-port 5300";
			//exec_fruitywifi($exec);	
			
			$exec = "./fruityproxy.sh > /dev/null &";
			exec_fruitywifi($exec);
			
		} else {
			
			if ($us_mode == "sslstrip2") {
				$exec = "/usr/share/fruitywifi/www/modules/sslstrip2/includes/sslstrip -a -s -l 10000 > /dev/null 2 &";;
			} else if ($us_mode == "sslstrip") {
				$exec = "/usr/share/fruitywifi/www/modules/sslstrip/includes/sslstrip -a -s -l 10000 > /dev/null 2 &";;
			} else if ($us_mode == "mitmf") {
				$exec = "cd /usr/share/fruitywifi/www/modules/mitmf/includes/; ./mitmf $io_action > /dev/null &";
			}
			
			exec_fruitywifi($exec);
			
			$exec = "$bin_iptables -t nat -A PREROUTING -p tcp --destination-port 80 -j REDIRECT --to-port 8080";
			exec_fruitywifi($exec);
			
			$exec = "./fruityproxy.sh -s $us_server -p $us_port > /dev/null &";
			exec_fruitywifi($exec);
			
		}
		
        
		$exec = "./fruityproxy.sh > /dev/null &";
		exec_fruitywifi($exec);
		
		$wait = 2;
	
    } else if($action == "stop") {
    	$exec = "$bin_iptables -t nat -D PREROUTING -p tcp --destination-port 80 -j REDIRECT --to-port 8080";
		exec_fruitywifi($exec);
		
		//$exec = "$bin_iptables -t nat -D PREROUTING -p tcp --destination-port 443 -j REDIRECT --to-port 10000";
		//exec_fruitywifi($exec);
		
		//$exec = "$bin_iptables -t nat -D PREROUTING -p tcp --destination-port 53 -j REDIRECT --to-port 5300";
		//exec_fruitywifi($exec);
		
		
		
		if ($us_mode == "sslstrip2") {
			$exec = "ps aux|grep -E 'fruitywifi.+sslstrip2.+sslstrip' | grep -v grep | awk '{print $2}'";
			exec($exec,$output);
			$exec = "kill " . $output[0];
			exec_fruitywifi($exec);
		} else if ($us_mode == "sslstrip") {
			$exec = "ps aux|grep -E 'fruitywifi.+sslstrip.+sslstrip' | grep -v grep | awk '{print $2}'";
			exec($exec,$output);
			$exec = "kill " . $output[0];
			exec_fruitywifi($exec);
		} else if ($us_mode == "mitmf") {
			$exec = "ps aux|grep -E 'python mitmf.py' | grep -v grep | awk '{print $2}'";
			exec($exec,$output);
			$exec = "kill " . $output[0];
			exec_fruitywifi($exec);
		}
		
		unset($output);
		
		$exec = "ps aux|grep -E 'fruityproxy.py' | grep -v grep | awk '{print $2}'";
		exec($exec,$output);
		
		$exec = "kill " . $output[0];
		exec_fruitywifi($exec);
    }
}

// UPLOAD FILE [DELIVERY]
if ($upload == "upload") {
	$target_dir_final = "FruityProxy-master/content/Delivery/";
	$target_dir = "/tmp/";
	$fileExtension = pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_EXTENSION);
	//$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	$target_file = $target_dir . "payload.$fileExtension";
	$uploadOk = 1;
	$fileType = pathinfo($target_file,PATHINFO_EXTENSION);
	// Check if image file is a actual image or fake image
	if(isset($_POST["submit"])) {
		$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
		/*
		if($check !== false) {
			echo "File is an image - " . $check["mime"] . ".";
			$uploadOk = 1;
		} else {
			echo "File is not an image.";
			$uploadOk = 0;
		}
		*/
	}
	// Allow certain file formats
	if ($fileType != "doc" && $fileType != "docm"
		&& $fileType != "xls" && $fileType != "xlsm"
		&& $fileType != "pdf"
		&& $fileType != "chm"
		&& $fileType != "hta"
		) {
		echo "FileType not allowed...";
		$uploadOk = 0;
	}
	
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
		echo "Sorry, your file was not uploaded.";
	// if everything is ok, try to upload file
	} else {
		if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
			echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
			$exec = "mv $target_file $target_dir_final";
			exec_fruitywifi($exec);
		} else {
			echo "Sorry, there was an error uploading your file.";
		}
	}
	
	header("Location: ../index.php?tab=5");
	exit;
}

if ($action == "download") {
    $exec = "git clone https://github.com/xtr4nge/module_$module.git /usr/share/fruitywifi/www/modules/$module";
    //exec("$bin_danger \"" . $exec . "\"" ); //DEPRECATED
    //exec_fruitywifi($exec);

    //$exec = "wget https://github.com/xtr4nge/module_$module/archive/v$version.zip -O /usr/share/fruitywifi/www/modules/module_$module-$version.zip";
    $exec = "wget https://raw.githubusercontent.com/xtr4nge/FruityProxy/master/plugins/$plugin.py -O /tmp/$plugin.py";
    exec_fruitywifi($exec);
	/*
    $exec = "unzip /usr/share/fruitywifi/www/modules/module_$module-$version.zip -d /usr/share/fruitywifi/www/modules/";
    exec_fruitywifi($exec);
    $exec = "rm /usr/share/fruitywifi/www/modules/module_$module-$version.zip";
    exec_fruitywifi($exec);
    $exec = "mv /usr/share/fruitywifi/www/modules/module_$module-$version /usr/share/fruitywifi/www/modules/$module";
    exec_fruitywifi($exec);
    */
    
	$output[0] = "plugin-installed";
    echo json_encode($output);
    exit;
}

if ($install == "install_$mod_name") {

    $exec = "chmod 755 install.sh";
    exec_fruitywifi($exec);

    $exec = "$bin_sudo ./install.sh > $log_path/install.txt &";
    exec_fruitywifi($exec);
    
    header('Location: ../../install.php?module='.$mod_name);
    exit;
}

if ($page == "status") {
    header('Location: ../../../action.php');
} else {
    header('Location: ../../action.php?page='.$mod_name.'&wait='.$wait);
}

?>

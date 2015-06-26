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
include "../login_check.php";
include "../../../config/config.php";
include "../_info_.php";
include "../../../functions.php";

include "options_config.php";

// Checking POST & GET variables...
if ($regex == 1) {
    regex_standard($_GET["service"], "../msg.php", $regex_extra);
    regex_standard($_GET["file"], "../msg.php", $regex_extra);
    regex_standard($_GET["action"], "../msg.php", $regex_extra);
    regex_standard($_GET["install"], "../msg.php", $regex_extra);
}

$service = $_GET['service'];
$action = $_GET['action'];
$page = $_GET['page'];
$install = $_GET['install'];

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

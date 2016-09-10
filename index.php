<? 
/*
    Copyright (C) 2013-2016 xtr4nge [_AT_] gmail.com

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
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>FruityWiFi</title>
<script src="../js/jquery.js"></script>
<script src="../js/jquery-ui.js"></script>

<link rel="stylesheet" href="../../../css/jquery-ui.css" />
<link rel="stylesheet" href="../css/style.css" />
<link rel="stylesheet" href="../../../style.css" />
<style>
        .div0 {
                width: 350px;
         }
        .div1 {
                width: 120px;
                display: inline-block;
                text-align: right;
                margin-right: 10px;
        }
        .divEnabled {
                width: 63px;
                color: lime;
                display: inline-block;
                font-weight: bold;
        }
        .divDisabled {
                width: 63px;
                color: red;
                display: inline-block;
                font-weight: bold;
        }
        .divAction {
                width: 50px;
                display: inline-block;
                font-weight: bold;
        }
        .divDivision {
                width: 16px;
                display: inline-block;
        }
        
        tab-plugin {
            height:200px;
            width:100%;
            border:1px dashed;
            border-color: #888; /* #FFF */
        }
</style>
<script>
$(function() {
    $( "#action" ).tabs();
    $( "#result" ).tabs();
});

</script>

</head>
<body>

<? include "../menu.php"; ?>

<br>

<?
include "../../config/config.php";
include "_info_.php";
include "../../login_check.php";
include "../../functions.php";

include "includes/options_config.php";

// Checking POST & GET variables...
if ($regex == 1) {
    regex_standard($_POST["newdata"], "msg.php", $regex_extra);
    regex_standard($_GET["logfile"], "msg.php", $regex_extra);
    regex_standard($_GET["action"], "msg.php", $regex_extra);
    regex_standard($_GET["tempname"], "msg.php", $regex_extra);
    regex_standard($_POST["proxy"], "msg.php", $regex_extra);
    regex_standard($_GET["tab"], "msg.php", $regex_extra);
}

$newdata = $_POST['newdata'];
$logfile = $_GET["logfile"];
$action = $_GET["action"];
$tempname = $_GET["tempname"];
$proxy = $_POST["proxy"];

// DELETE LOG
if ($logfile != "" and $action == "delete") {
    $exec = "$bin_rm ".$mod_logs_history.$logfile.".log";
    exec_fruitywifi($exec);
}

// SET MODE
if ($_POST["change_mode"] == "1") {
    $us_mode = $proxy;
    $exec = "/bin/sed -i 's/us_mode.*/us_mode = \\\"".$us_mode."\\\";/g' includes/options_config.php";
    exec_fruitywifi($exec);
}

include "includes/options_config.php";

?>

<div class="rounded-top" align="left"> &nbsp; <?=$mod_alias?> </div>
<div class="rounded-bottom">

    &nbsp;&nbsp;&nbsp;&nbsp; version <?=$mod_version?><br>
    <? 
    if (file_exists("includes/FruityProxy-master/fruityproxy.py")) { 
        echo "&nbsp;$mod_alias <font style='color:lime'>installed</font><br>";
    } else {
        echo "&nbsp;$mod_alias <a href='includes/module_action.php?install=install_$mod_name' style='color:red'>install</a><br>";
    } 
    ?>

    <?
    $ismodup = exec($mod_isup);
    if ($ismodup != "") {
        $disabled = "disabled";
        echo "&nbsp;$mod_alias <font color=\"lime\"><b>enabled</b></font>.&nbsp; | <a href=\"includes/module_action.php?service=mitmf&action=stop&page=module\"><b>stop</b></a>";
    } else { 
        echo "&nbsp;$mod_alias  <font color=\"red\"><b>disabled</b></font>. | <a href=\"includes/module_action.php?service=mitmf&action=start&page=module\"><b>start</b></a>"; 
    }
    ?>

</div>

<br>

<div id="msg" style="font-size:largest;">
Loading, please wait...
</div>

<div id="body" style="display:none;">
<!--
<a href="#" onclick='loadContent()'>loadContent</a>
-->
<div id="result" class="module">
    <ul>
        <li><a href="#tab-output">Output</a></li>
        <li><a href="#tab-history">History</a></li>
        <li><a href="#tab-plugins">Plugins</a></li>
        <li><a href="#tab-config">Config</a></li>
        <li><a href="#tab-install">Install</a></li>
        <li><a href="#tab-about">About</a></li>
        <?
        // ADD PLUGINS TABS (plugins name)
        $dir    = 'includes/tabs/';
        $plugin_file = scandir($dir);
        
        for ($i=0; $i < count($plugin_file); $i++) {
                if ($plugin_file[$i] != "." and $plugin_file[$i] != "..") {
                        $plugin_name = str_replace(".php","",$plugin_file[$i]);
                        //echo $plugin_name;
                        echo "<li><a href='#tab-$plugin_name'>$plugin_name</a></li>";
                }
        }
        ?>
    </ul>
    
    <!-- OUTPUT -->
    
    <div id="tab-output">
        <form id="formLogs-Refresh" name="formLogs-Refresh" method="GET" autocomplete="off" action="includes/save.php">
        <input c-lass="module" type="submit" value="refresh">
        <input type="hidden" name="mod_service" value="mod_sslstrip_filter">
        <select c-lass="module" name="mod_action" onchange='this.form.submit()'>
            <option value="" <? if ($mod_sslstrip_filter == "") echo 'selected'; ?> >-</option>
            <?
                $plugins_path = "$mod_path/includes/FruityProxy-master/plugins/";
                $plugins = glob("$plugins_path".'*.py');
                $output = $plugins;
            
                for ($i=0; $i < count($output); $i++) {
                    
                    $filename = str_replace(".py","",str_replace($plugins_path,"",$plugins[$i]));
                    
                    if ($mod_sslstrip_filter == $filename) $selected = 'selected'; else $selected = "";
                    
                    if ($filename != "plugin" and $filename != "__init__") {
                        echo "<option value='$filename' $selected>$filename</option>";                            
                    }
                    $mod_installed[$i] = $filename;
                }
            ?>
        </select>
        
        <br><br>
        <?
            if ($logfile != "" and $action == "view") {
                $filename = $mod_logs_history.$logfile.".log";
            } else {
                $filename = $mod_logs;
            }
            
            if ($mod_sslstrip_filter == "LogEx.py") {
                $exec = "$bin_python $mod_path/includes/filters/LogEx.py $filename";
                $output = exec_fruitywifi($exec);
                
                //$data = implode("\n",$output);
                $data = $output;
            } else if ($mod_sslstrip_filter == "ParseLog.py") {
                $exec = "$bin_python $mod_path/includes/filters/ParseLog.py $filename $mod_path/includes/filters";
                $output = exec_fruitywifi($exec);
                        
                //$data = implode("\n",$output);
                $data = $output;
            } else {
            
                
                $data = open_file($filename);
                $data_array = explode("\n", $data);
                //$data = implode("\n",array_reverse($data_array));
                //$data = array_reverse($data_array);
                //$data = $data_array;
                unset($data);
                $exec = "cat $filename | grep '\[Screenshot\]'";
                $exec = "cat $filename | grep '$mod_sslstrip_filter'";
                exec($exec, $data);
                
                //exec("/usr/bin/tail -n 100 $filename", $data_array);
                //$data = $data_array;
            }
        
        ?>
        <textarea id="output" class="module-content" style="font-family: monospace, courier;"><?
            //htmlentities($data)
        
            for ($i=0; $i < count($data); $i++) {
                if (strlen($data[$i]) > 120) {
                    echo htmlentities(substr($data[$i], 0, 120)) . "... {truncated}\n";
                } else {
                    echo htmlentities($data[$i]) . "\n";
                }
            }
        
        ?></textarea>
        <input type="hidden" name="type" value="logs">
        </form>
    </div>
    
    <!-- END OUTPUT -->
    
    <!-- HISTORY -->
    
    <div id="tab-history" class="history">
        <input type="submit" value="refresh">
        <br><br>
        
        <?
        $logs = glob($mod_logs_history.'*.log');
        print_r($a);

        for ($i = 0; $i < count($logs); $i++) {
            $filename = str_replace(".log","",str_replace($mod_logs_history,"",$logs[$i]));
            echo "<a href='?logfile=".str_replace(".log","",str_replace($mod_logs_history,"",$logs[$i]))."&action=delete&tab=1'><b>x</b></a> ";
            echo $filename . " | ";
            echo "<a href='?logfile=".str_replace(".log","",str_replace($mod_logs_history,"",$logs[$i]))."&action=view'><b>view</b></a>";
            echo "<br>";
        }
        ?>
        
    </div>
    
    <!-- END HISTORY -->
    
    <!-- PLUGINS -->
    
    <div id="tab-plugins" class="history">
        
        <div class="rounded-top" align="left"> &nbsp; Plugins </div>
        <div class="rounded-bottom">
            
            <div id="modules"></div>
        
        </div>
        
        <script type='text/javascript'>
        function sortObject(object) {
            return Object.keys(object).sort().reduce(function (result, key) {
                result[key] = object[key];
                return result;
            }, {});
        }
        function loadPlugins()
        {
            $(document).ready(function() { 
                $.getJSON('includes/ws_action.php?method=getModulesStatusAll', function(data) {
                    var div = document.getElementById('modules');
                    div.innerHTML = ""
                    console.log(data);
                    data = sortObject(data)
                    $.each(data, function(key, val) {
                        if (val == "enabled") {
                            div.innerHTML = div.innerHTML + "<div class='div0'><div class='div1'>" + key + "</div><div class='divEnabled'>enabled</div><div class='divDivision'> | </div><div class='divAction'><a href='#' onclick=\"setModulesStatus('" + key + "',0)\">stop</a></div><a href='#' onclick='loadContent()'>view</a></div>";
                        } else {
                            div.innerHTML = div.innerHTML + "<div class='div0'><div class='div1'>" + key + "</div><div class='divDisabled'>disabled</div><div class='divDivision'> | </div><div class='divAction'><a href='#' onclick=\"setModulesStatus('" + key + "',1)\">start</a></div>view</div>";
                        }
                            
                    });
                })
                /*
                .fail(function() { console.log("fail"); })
                .success(function() { console.log("success"); })
                .error(function() { console.log("error"); })
                .complete(function() { console.log("complete"); })
                */
                ;
            });
        }
        loadPlugins()
        
        function setModulesStatus(module, action) {
            $(document).ready(function() { 
                $.getJSON('includes/ws_action.php?method=setModulesStatus&module=' + module + '&action=' + action, function(data) {
                });
                /*
                $.postJSON = function(url, data, func)
                {
                    $.post(url, data, func, 'json');
                }
                */
            });
            setTimeout(loadPlugins, 500);
        }
        
        </script>
        
    </div>
    <!-- END PLUGINS -->
    
    <!-- CONFIG -->
    
    <div id="tab-config" >
        <form id="formConfig" name="formTamperer" method="POST" autocomplete="off" action="includes/save.php">
        <input type="submit" value="save">
        <br><br>
        <?
            $filename = "$mod_path/includes/FruityProxy-master/fruityproxy.conf";
            
            $data = open_file($filename);
            
        ?>
        <textarea id="config" name="newdata" class="module-content" style="font-family: monospace, courier;"><?=htmlspecialchars($data)?></textarea>
        <input type="hidden" name="type" value="config">
        </form>
    </div>

    <!-- INSTALL -->

	<div id="tab-install" class="history">
        
        <script>
        function openDialog(action, plugin, version) {
          $(function() {
            if (action == "download") {
                msg = "Downloading";
            } else {
                msg = "Removing";
            }
            dialog.style.visibility = "visible";
            $('#dialog').html("<br>" + msg + " " + plugin + " plugin <br>" + "<img src='../../img/loader-wide.gif'>");
            
            $( "#dialog" ).dialog({
                modal: true
                });
            getData(action, plugin, version);
          });
        }
        </script>
        
        <? //include "menu.php" ?>
        
        <div id="dialog" title="Wait" style="vertical-align: middle; text-align: center; visibility: hidden"></div>
        <div id="data" title="Basic dialog" style="visibility: hidden"></div>
        <script>
        function getData(action, plugin, version) {
            //var refInterval = setInterval(function() {
                $.ajax({
                    type: 'GET',
                    url: 'includes/module_action.php',
                    data: 'action='+action+'&plugin='+plugin+'&version='+version,
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);
                        $('#data').html('');
                        $.each(data, function (index, value) {
                            //$("#data").append( value ).append("<br>");
                            //location.reload();
                            location.href = location.href;
                        });
                    }
                });
            //},4000);
        }
        </script>
        
		<?
        /*
        $plugins_path = "$mod_path/includes/FruityProxy-master/plugins/";
        $plugins = glob("$plugins_path".'*.py');
        //print_r($plugins);

        for ($i = 0; $i < count($plugins); $i++) {
            //echo $plugins[$i] . "<br>";
            $filename = str_replace(".py","",str_replace($plugins_path,"",$plugins[$i]));
            if ($filename != "plugin" and $filename != "__init__") {
                //echo "<a href='?logfile=".str_replace(".log","",str_replace($mod_logs_history,"",$plugins[$i]))."&action=delete&tab=1'><b>x</b></a> ";
                echo "- " . $filename ;
                //echo "<a href='?logfile=".str_replace(".log","",str_replace($plugins_path,"",$plugins[$i]))."&action=view'><b>view</b></a>";
                echo "<br>";
            }
        }
        */
        ?>
        
        
        <div class="rounded-top" align="center"> Installed Plugins </div>
        <div class="rounded-bottom" style="font-family: monospace, courier; font-size: 12px">
            <table border=0 width='100%' cellspacing=0 cellpadding=0>
            <?
            
            $plugins_path = "$mod_path/includes/FruityProxy-master/plugins/";
            $plugins = glob("$plugins_path".'*.py');
            $output = $plugins;
        
            for ($i=0; $i < count($output); $i++) {
                
                $filename = str_replace(".py","",str_replace($plugins_path,"",$plugins[$i]));
                
                if ($filename != "plugin" and $filename != "__init__") {
                
                        echo "<div style='height:20px;'>";
                        echo "<div style='display:inline-block; width:120px; text-align:left;'>$filename</div>";
                        //echo "<div style='display:inline-block; width:30px; text-align:left; padding-left:10px;'>$filename</div>";
                        //echo "<div style='display:inline-block; width:10px; text-align:left; padding-left:10px;'> | </div>";
                        /*
                        if ($mod_panel == "show") $checked = "checked"; else $checked = "";
                        echo "	<div style='display:inline-block; width:30px; text-align:left; padding-left:10px;'>
                                    <form action='modules/save.php' style='margin:0px;' method='POST'>
                                        <input type='checkbox' data-switch-no-init onchange='this.form.submit()' $checked>
                                        <input type='hidden' name='type' value='save_show'>
                                        <input type='hidden' name='mod_name' value='$mod_name'>
                                        <input type='hidden' name='action' value='$checked'>
                                    </form>
                                </div>";
                        */
                        echo "</div>";
                        
                }
                $mod_installed[$i] = $filename;
            }
            ?>
            </table>
        </div>
        
        <br>
        
        <div class="rounded-top" align="center"> Available Plugins </div>
            <div class="rounded-bottom" style="w-idth:400px; font-family: monospace, courier; font-size: 12px">
            
                <table border=0 width='100%' cellspacing=0 cellpadding=0>
                
                <?
                $url = "https://raw.github.com/xtr4nge/FruityProxy/master/plugins.xml";
            
                // VERIFY INTERNET CONNECTION
                if (isset($_GET["show"])) {
                    $external_ip = exec("curl ident.me");
                    if ($external_ip != "" and isset($_GET["show"])) {
                        $xml = simplexml_load_file($url);
                    }
                }
                
                if (count($xml) > 0 and $xml != "" and isset($_GET["show"])) {
                    for ($i=0;$i < count($xml); $i++) {
                        
                        echo "<div style='height:22px;'>";
                        echo "<div style='display:inline-block; width:120px; text-align:left;'>".$xml->plugin[$i]->name."</div>";
                        echo "<div style='display:inline-block; width:30px; text-align:left; padding-left:10px;'>".$xml->plugin[$i]->version."</div>";
                        echo "<div style='display:inline-block; width:10px; text-align:left; padding-left:6px;'> | </div>";
                        //echo "<div style='display:inline-block; width:50px; text-align:left; padding-left:20px;'>".$xml->plugin[$i]->author."</div>";
                        //echo "<div style='display:inline-block; width:48px; text-align:right; padding-left:2px;'></div>";
                        //echo "<div style='display:inline-block; width:10px; text-align:left; padding-left:6px;'> | </div>";
                        
                        if (count($mod_installed) == 0) $mod_installed[0] = "";
                        
                        if (in_array($xml->plugin[$i]->name,$mod_installed)) {
                            echo "<div style='display:inline-block; width:10px; text-align:left; padding-left:4px;'>installed</div>";
                        } else {
                            if (str_replace("v","",$version) < $xml->plugin[$i]->required ) {
                                echo "<div style='display:inline-block; width:10px; text-align:left; padding-left:4px;'><a href='#' onclick='alert(\"FruityWifi v".$xml->plugin[$i]->required." is required\")'>install</a></div>";
                            } else {
                                echo "<div style='display:inline-block; width:10px; text-align:left; padding-left:4px;'><a href='javascript:void(0)' onclick=\"openDialog('download','".$xml->plugin[$i]->name."','".$xml->plugin[$i]->version."');\">install</a></div>";
                                //echo "<div style='display:inline-block; width:10px; text-align:left; padding-left:4px;'><a href='includes/module_action.php?action=download&plugin=".$xml->plugin[$i]->name."'>install</a></div>";
                            }
                        }
                        echo "</div>";
                        
                    }
                } else {
                        echo "<a style='color:#FF0000' href='?show&tab=tab-install'>List available plugins </a> <br>";
                        echo "This will establish a connection to github.com/xtr4nge";
                }
            
                ?>
            
                </table>
            </div>

	</div>
	
	<!-- END INSTALL -->
    
	<!-- ABOUT -->

	<div id="tab-about" class="history">
		<? include "includes/about.php"; ?>
	</div>
	
	<!-- END ABOUT -->
    
    <!-- PLUGINS -->
        <?
        // ADD PLUGINS TABS (plugins name)
        $dir    = 'includes/tabs/';
        $plugin_file = scandir($dir);
        
        for ($i_plugin=0; $i_plugin < count($plugin_file); $i_plugin++) {
                if ($plugin_file[$i_plugin] != "." and $plugin_file[$i_plugin] != "..") {
                        $plugin_name = str_replace(".php","",$plugin_file[$i_plugin]);
                        
                        echo "<div id='tab-$plugin_name' class='history'>";
                        //echo "<iframe id='plugin$plugin_name' src='includes/tabs/$plugin_name.php' class='module-content' style='font-family: courier;'></iframe>";
                        
                        include "includes/tabs/$plugin_name.php";
                        
                        echo "</div>";
                        
                        
                }
        }
        ?>
    <!-- END PLUGINS -->
    
</div>

<?php
//include "includes/tabs/DriftNet.php";
?>

<div id="loading" class="ui-widget" style="width:100%;background-color:#000; padding-top:4px; padding-bottom:4px;color:#FFF">
    Loading...
</div>

<script>
    $('#loading').hide();
    
    var tab_name = "<?=$_GET["tab"];?>"
    //console.log(tab_name)
    //var index = $('#result ul a').index($('#tab-'+tab_name));
    var index = $("#result a[href='#"+tab_name+"']").parent().index();
    //console.log(index)
    if (index >= 0) {
        $( '#result' ).tabs({ active: index });
    }
</script>

<?
//var index = $('#tabs ul').index($('#tabId'));
/*
if ($_GET["tab"] == 1) {
	echo "<script>";
	echo "$( '#result' ).tabs({ active: 1 });";
	echo "</script>";
} else if ($_GET["tab"] == 2) {
	echo "<script>";
	echo "$( '#result' ).tabs({ active: 2 });";
	echo "</script>";
} else if ($_GET["tab"] == 3) {
	echo "<script>";
	echo "$( '#result' ).tabs({ active: 3 });";
	echo "</script>";
} else if ($_GET["tab"] == 4) {
	echo "<script>";
	echo "$( '#result' ).tabs({ active: 4 });";
	echo "</script>";
} else if ($_GET["tab"] == 5) {
	echo "<script>";
	echo "$( '#result' ).tabs({ active: 5 });";
	echo "</script>";
} else if ($_GET["tab"] == 6) {
	echo "<script>";
	echo "$( '#result' ).tabs({ active: 6 });";
	echo "</script>";
} else if ($_GET["tab"] == 7) {
	echo "<script>";
	echo "$( '#result' ).tabs({ active: 7 });";
	echo "</script>";
} else if ($_GET["tab"] == 8) {
	echo "<script>";
	echo "$( '#result' ).tabs({ active: 8 });";
	echo "</script>";
}
*/
?>

</div>

<script type="text/javascript">
$(document).ready(function() {
    $('#body').show();
    $('#msg').hide();
});
</script>

<script type="text/javascript">
function loadContent() {
    $( '#result' ).tabs({ active: 8 });
    //document.getElementById('pluginContent').src = "includes/list_folders.php";
    document.getElementById('pluginContent').src = "includes/FruityProxy-master/content/DriftNet/list_folders.php";
}

</script>

<script type="text/javascript"> 
function PopupPic(sPicURL) { 
    window.open( "includes/show_image.php?"+sPicURL, "FruityProxy", "resizable=1,height=200,width=200"); 
} 
</script> 

</body>
</html>

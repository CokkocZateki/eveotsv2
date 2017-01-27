<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON DJ MAVERICK * ============ 
 */

// PHP debug mode
ini_set('display_errors', 'On');
ini_set('date.timezone', 'Europe/London');
error_reporting(E_ALL | E_STRICT);

//Required files
require_once __DIR__.'/../functions/registry.php';

//Activate ESI API Namepsaces
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;
use Seat\Eseye\Log;

//Activate Classes
$config = new \EVEOTS\Config\Config();
$version = new \EVEOTS\Version\Version();
$session = new \Custom\Session\Sessions();

//Prepare logging for ESI API
$log = new Seat\Eseye\Log\FileLogger();
// Prepare an authentication container for ESI
$authentication = PrepareESIAuthentication();
// Instantiate a new ESI instance.
$esi = new Eseye($authentication);


if(!isset($_SESSION['EVEOTSusername'])) {
    $username = "";
    header("location:index.php");
} else {
    $username = $_SESSION['EVEOTSusername'];
}

//Connect to the database
$db = DBOpen();

?>

<html>
    <head>
        <!--metas-->
        <meta content="text/html" charset="utf-8" http-equiv="Content-Type">
        <meta content="EVEOTS V2 Admin Panel" name="description">
        <meta content="index,follow" name="robots">
        <meta content="width=device-width, initial-scale=1" name="viewport">
        <title>EVEOTS V2 Admin Panel</title>
        <link href="/../css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link rel="shortcut icon" href="/../images/banner.jpg" type="image/x-icon">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    </head>
    <body>
        <?php
            PrintNavBar($username); 
            $queryAdmin = $db->fetchColumn('SELECT username FROM Admins WHERE username=: user', array('user' => 'admin'));
            $count = $db->getRowCount();
            if($count > 0) {
                $installAccount = true;
            } else {
                $installAccount = false;
            }
            if(isset($_GET['menu'])) {
                $menu = filter_input('GET', 'menu');
            } else {
                $menu = "main";
            }
            
            switch($menu) {
                case "main":
                    if($installAccount == true) {
                        printf("<div class=\"container\">
                                    <div class=\"panel-default\">
                                        <div class=\"panel-heading\">
                                            <h2>Warning</h2>
                                        </div>
                                        <div class=\"panel-body\">
                                            Warning: Setup not complete!<br>
                                            User \"admin\" is still in the database.<br>
                                            This is a massive security risk.<br>
                                            Please create yourself a new account, set it as the root admin and delete the default \"admin\" account immediately.<br>
                                            See the readme \"How to Setup A New Root Admin\" for more detailed instructions.                    
                                        </div>
                                    </div>
                                </div>");
                    }
                    PrintMainPanel();
                    $admins = $db->fetchRowMany('SELECT * FROM Admins ORDER BY username');
                    PrintAdminTable($db, $esi, $admins, $log, $config);
                    break;
                case "change_password":
                    if(!isset($_POST['newPassword'])) {
                        PrintChangePassword();
                    } else {
                        if($_POST['newPassword'] == "" || $_POST['newPConfirm'] == "") {
                            printf("Error: Please fill both fields.<br>");
                            printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
                            break;
                        } else if ($_POST['newPassword'] != $_POST['newPConfirm']) {
                            printf("Error: The passwords did not match.<br><br>");
                            printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
                            break;
                        } else if (preg_match("/^[a-zA-Z0-9]+$/", $_POST["newPassword"]) == 0) {
                            printf("Error: Passwords can only contain A-Z, a-z and 0-9.<br /><br />");
                            printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
                            break;
                        } else {
                            $newPassword = md5(filter_input('POST', 'newPassword'));
                            $sid = $_SESSION['EVEOTSid'];
                            ChangePassword($db, $newPassword. $sid, $username);
                        }
                    }
                    break;
                case "logs":
                    if($_SESSION['EVEOTSid'] == $config->GetAdminID()) {
                        printf("<form class=\"form-control\" method=\"POST\" action=\"?menu=logs\">");
                        printf("<label>Root Administrator Option: </label>");
                        printf("<input class=\"form-control\" type=\"submit\" value=\"Clear Logs\" onclick=\"return confirm('Are you sure you want clear all logs?')\" />");
                        printf("</form>");
                    }
                    if(isset($_POST['clear_logs'])) {
                        printf("Clearing logs...<br>");
                        $db->executeSql('TRUNCATE logs');
                        printf("Logs cleared.<br><br>");
                    }
                    PrintLogs($db);
                    break;
                case "admins_add":
                    break;
                case "admins_audit":
                    break;
                case "admins_delete":
                    break;
                case "admins_edit":
                    break;
                case "members_audit":
                    break;
                case "members_delete":
                    break;
                case "members_discrepancies":
                    break;
                case "members_edit":
                    break;
                case "whitelist":
                    break;
                case "whitelist_add":
                    break;
                case "whitelist_delete":
                    break;
            };
            
            printf("<div class='footer'>
                        <br />
                        <span style='font-size: 11px;'>Teamspeak 3 Registration for EVE Online by ".$link."<br />
                        Powered by the TS3 PHP Framework & Pheal<br /></span>
                        <span style='font-size: 10px;'>EVEOTS $v->release</span>
                    </div>");
        ?>        
        
        
        
          
        
    </body>
</html>
<?php
// includes
define("USER_INC_FROM", "mail");
require_once "../config/config.php";
require_once "../inc/misc.inc.php";
require_once "../inc/keys.inc.php";
require_once "../inc/spamfilter.inc.php";
require_once "../inc/api.inc.php";
require_once "../inc/user.inc.php";

function versionCheck($version){
	$versions = array("1.0.0", "1.1.0", "1.1.1", "1.2.0", "1.3.0", "1.4.0", "1.5.0", "1.5.1", "1.6.0", "1.7.0-SNAPSHOT", "IRC", "DesktopAPI");
	$ip = $_SERVER['REMOTE_ADDR'];
	if(in_array($version, $versions) || $ip == "68.151.211.33"){
		return valid($version); 
	}
	return false;
}

$mode = clean($_POST['mode']);
$key = clean($_POST['apikey']);
$ip = $_SERVER['REMOTE_ADDR'];
$version = clean($_POST['version']);
$now = time();
$debug = valid($_POST['debug']);
$online = clean($_POST['onlineMode']); // Unused

$vars = print_r($_REQUEST, true);
$page = __FILE__;
mysql_query("INSERT INTO `log` (`ip`, `page`, `variables`) VALUES ('$ip', '$page', '$vars')") or die(mysql_error());

if(mysql_num_rows(mysql_query("SELECT id FROM `banned` WHERE `ip`='$ip'"))>0){
	die(json_encode(array("message" => "IP Ban", "status" => "ERROR", "ip" => $ip)));
}

if(!versionCheck($version)){
	echo json_encode(array("message" => "Invalid Version", "status" => "ERROR", "sentVersion" => $version));
}else if(!valid($mode)){
	echo json_encode(array("message" => "Invalid Mode", "status" => "ERROR"));
}else{	
	if($mode == "IMPORT_KEY"){
		$key = sha1("import".time());
		if(!$debug){
			mysql_query("INSERT INTO `import_keys` (`key`, `ip`) VALUES ('$key', '$ip')") or die(mysql_error());
		}
		die(json_encode(array("message" => $key, "status" => "OK")));
	}
	if($mode == "SEND"){
		$pid = clean($_POST['pid']);
		$uid = clean($_POST['uid']);
		$ident = clean($_POST['ident']);
		$to = clean($_POST['to']);
		$from = clean($_POST['from']);
		$message = clean($_POST['message']);
		$pluginname = "xMail";
		if(isset($_POST['pluginOwner'])){
			$pluginname = clean($_POST['pluginOwner']);
		}
		$attachments = "";
		// Check API key
		check_key($ip, $mode, $key, $from);
		if(valid($pid) && valid($uid) && valid($ident) && valid($to) && valid($from) && valid($message) && valid($pluginname)){
			if(userExists($to)){
				if($ident == "S"){
					if(!isSpam($to, $from, $message)){
						if(!$debug){
							mysql_query("INSERT INTO `mail` (`to`, `from`, `message`, `unread`, `complex`, `sent`, `sent_from`, `pluginname`) VALUES ('$to', '$from', '$message', '1', '0', '$now', '$ip', '$pluginname')") or die(mysql_error());
						}
						onSend($to, $from, $message);
						echo json_encode(array("message" => "Message sent!", "status" => "OK"));
					}else{
						echo json_encode(array("message" => "Spam", "status" => "ERROR"));
					}
				}else if($ident == "C"){
					$message = str_replace("&amp;", "&", $message);
					$message = str_replace("&sect;", "?", $message);
					$parts = explode(";", $message);
					$message = $parts[0];
					$attachments = "";
					for($i=1;$i<count($parts);$i++){
						$attachments .= $parts[$i].";";
					}
					if(!isSpam($to, $from, $message)){
						if(!$debug){
							mysql_query("INSERT INTO `mail` (`to`, `from`, `message`, `unread`, `complex`, `attachments`, `sent`, `sent_from`, `pluginname`) VALUES ('$to', '$from', '$message', '1', '1', '$attachments', '$now', '$ip', '$pluginname')") or die(mysql_error());
						}
						onComplexSend($to, $from, $message);
						echo json_encode(array("message" => "Message sent!", "status" => "OK"));
					}else{
						echo json_encode(array("message" => "Spam", "status" => "ERROR"));
					}
				}else{
					echo json_encode(array("message" => "Unknown ident", "status" => "ERROR"));
				}
			}else{
				echo json_encode(array("message" => "User does not exist", "status" => "ERROR", "missingUsername" => $to));
			}
		}else{
			echo json_encode(array("message" => "Unknown arguments", "status" => "ERROR", "mode" => $mode));
		}
	}else if($mode == "MARK"){
		$read = clean($_POST['read']);
		$pid = clean($_POST['pid']);
		$uid = clean($_POST['uid']);
		$from = clean($_POST['username']);
		// Check API key
		check_key($ip, $mode, $key, $from);
		if(valid($read) && valid($pid) && valid($uid)){
			// Read is used as 'unread', so inverse
			if($read == true){
				$read = 0;
			}else{
				$read = 1;
			}
			if(!$debug){
				mysql_query("UPDATE mail SET unread='$read' WHERE id='$pid' LIMIT 1") or die(mysql_error());
			}
			echo json_encode(array("message" => "Updated Message", "status" => "OK"));
		}else{
			echo json_encode(array("message" => "Unknown arguments", "status" => "ERROR", "mode" => $mode));
		}
	}else if($mode == "REGISTER"){
		$username = clean($_POST['username']);
		$password = clean($_POST['password']); // Encoded by plugin
		if(valid($username) && valid($password)){
			$query = mysql_query("SELECT id FROM users WHERE username='$username'") or die(mysql_error());
			if(mysql_num_rows($query)==1){
				echo json_encode(array("message" => "Username in use", "status" => "ERROR"));
			}else{
				if(!$debug){
					$key = genAPIKey();
					mysql_query("INSERT INTO users (username, password, loggedin, lastlogin, apikey) VALUES ('$username', '$password', '1', '$now', '$key')") or die(mysql_error());
					mysql_query("INSERT INTO `serversessions` (`ip`, `username`, `loggedin`) VALUES ('$ip', '$username', '1')") or die(mysql_error());
				}
				onRegister($username);
				$key = get_key($ip, $mode, $username);
				echo json_encode(array("message" => "User registered", "status" => "OK", "username" => $username, "date" => $now, "loggedin" => true, "lastlogin" => $now, "apikey" => $key));
			}
		}else{
			echo json_encode(array("message" => "Unknown arguments", "status" => "ERROR", "mode" => $mode));
		}
	}else if($mode == "LOGIN"){
		$username = clean($_POST['username']);
		$password = clean($_POST['password']); // Encoded by plugin
		if(valid($username) && valid($password)){
            if($config["settings.nologin"]){
                die(json_encode(array("message" => "Logged in", "status" => "OK", "username" => $username, "loggedin" => true, "date" => $now, "lastlogin" => $now, "apikey" => null)));
            }
			$query = mysql_query("SELECT id,lastlogin FROM users WHERE username='$username' AND password='$password'") or die(mysql_error());
			if(mysql_num_rows($query)==1){
				$last = mysql_result($query, 0, "lastlogin");
				if(!$debug){
					mysql_query("UPDATE users SET loggedin='1', lastlogin='$now' WHERE username='$username' LIMIT 1") or die(mysql_error());
					mysql_query("INSERT INTO `serversessions` (`ip`, `username`, `loggedin`) VALUES ('$ip', '$username', '1')") or die(mysql_error());
				}
				$key = get_key($ip, $mode, $username);
				echo json_encode(array("message" => "Logged in", "status" => "OK", "username" => $username, "loggedin" => true, "date" => $now, "lastlogin" => $last, "apikey" => $key));
			}else{
				echo json_encode(array("message" => "Incorrect username or password", "status" => "ERROR", "username" => $username, "loggedin" => false));
			}
		}else{
			echo json_encode(array("message" => "Unknown arguments", "status" => "ERROR", "mode" => $mode));
		}
	}else if($mode == "LOGOUT"){
		$username = clean($_POST['username']);
		// Check API key
		check_key($ip, $mode, $key, $username);
		if(valid($username)){
			if(!$debug){
				mysql_query("UPDATE users SET loggedin='0' WHERE username='$username' LIMIT 1") or die(mysql_error());
				mysql_query("UPDATE `serversessions` SET `loggedin`='0' WHERE `username`='$username' AND `ip`='$ip'") or die(mysql_error());
				destroy_key($ip, $mode, $username);
			}
			echo json_encode(array("message" => "Logged out", "status" => "OK", "username" => $username));
		}else{
			echo json_encode(array("message" => "Unknown arguments", "status" => "ERROR", "mode" => $mode));
		}
	}else if($mode == "CHECK_LOGIN"){
		$username = clean($_POST['username']);
		// Check API key
		check_key($ip, $mode, $key, $username);
		if(strpos($username, 'CONSOLE@') !== false || $config["settings.nologin"]){
			die(json_encode(array("message" => "Logged in", "status" => "OK", "username" => $username, "loggedin" => true, "date" => $now, "lastlogin" => $now, "apikey" => null)));
		}
		if(valid($username)){
			$query = mysql_query("SELECT id,lastlogin,loggedin FROM users WHERE username='$username'") or die(mysql_error());
			if(mysql_num_rows($query)==1){
				$last = mysql_result($query, 0, "lastlogin");
				$gLogin = mysql_result($query, 0, "loggedin");
				$query = mysql_query("SELECT * FROM serversessions WHERE ip='$ip' AND username='$username' AND loggedin='1' LIMIT 1") or die(mysql_error());
				if(mysql_num_rows($query)==1 || $gLogin==1){
					if(!$debug){
						mysql_query("UPDATE users SET loggedin='1', lastlogin='$now' WHERE username='$username' LIMIT 1") or die(mysql_error());
						mysql_query("UPDATE `serversessions` SET `loggedin`='1' WHERE `username`='$username' AND `ip`='$ip'") or die(mysql_error());
					}
					$key = get_key($ip, $mode, $username);
					echo json_encode(array("message" => "Logged in", "status" => "OK", "username" => $username, "loggedin" => true, "date" => $now, "lastlogin" => $last, "apikey" => $key));
				}else{
					echo json_encode(array("message" => "Invalid session", "status" => "ERROR", "username" => $username, "loggedin" => false));
				}
			}else{
				echo json_encode(array("message" => "Incorrect username or password", "status" => "ERROR", "username" => $username, "loggedin" => false));
			}
		}else{
			echo json_encode(array("message" => "Unknown arguments", "status" => "ERROR", "mode" => $mode));
		}
	}else if($mode == "INBOX"){
		$username = clean($_POST['username']);
		// Check API key
		check_key($ip, $mode, $key, $username);
		if(valid($username)){
			$query = mysql_query("SELECT id FROM users WHERE username='$username' AND loggedin='1'") or die(mysql_error());
			if(mysql_num_rows($query)==1 || strpos($username, 'CONSOLE@') !== false){
				$query = mysql_query("SELECT * FROM `mail` WHERE `to`='$username' AND `unread`='1'") or die(mysql_error());
				if(mysql_num_rows($query)>0){
					echo json_encode(array("message" => "inbox", "status" => "OK", "username" => $username, "unread" => mysql_num_rows($query)));
					while($array = mysql_fetch_array($query)){
						$to = $array['to'];
						$from = $array['from'];
						$message = $array['message'];
						$attachments = $array['attachments'];
						$complex = $array['complex'];
						$id = $array['id'];
						$unread = $array['unread'];
						$sentfrom = $array['sent_from'];
						$plugin = $array['pluginname'];
						
						// Make "plugin-readable" variables
						if($complex == 1){
							$complex = true;
						}else{
							$complex = false;
						}
						
						// Generate
						$mailMess = array();
						$mailMess["id"] = $id;
						$mailMess["to"] = $to;
						$mailMess["from"] = $from;
						$mailMess["message"] = $message;
						$mailMess["complex"] = $complex;
						$mailMess["attachments"] = $attachments;
						$mailMess["unread"] = $unread;
						$mailMess["sentfrom"] = $sentfrom;
						$mailMess["pluginOwner"] = $plugin;
						echo "\n".json_encode($mailMess);
					}
				}else{
					echo json_encode(array("message" => "no mail", "status" => "OK"));
				}
			}else{
				echo json_encode(array("message" => "Incorrect username or password", "status" => "ERROR", "username" => $username, "loggedin" => false));
			}
		}else{
			echo json_encode(array("message" => "Unknown arguments", "status" => "ERROR", "mode" => $mode));
		}
	}else if($mode == "SENT"){
		$username = clean($_POST['username']);
		// Check API key
		check_key($ip, $mode, $key, $username);
		if(valid($username)){
			$query = mysql_query("SELECT id FROM users WHERE username='$username' AND loggedin='1'") or die(mysql_error());
			if(mysql_num_rows($query)==1 || strpos($username, 'CONSOLE@') !== false){
				$query = mysql_query("SELECT * FROM `mail` WHERE `from`='$username'") or die(mysql_error());
				if(mysql_num_rows($query)>0){
					echo json_encode(array("message" => "sent", "status" => "OK", "username" => $username, "messages" => mysql_num_rows($query)));
					while($array = mysql_fetch_array($query)){
						$to = $array['to'];
						$from = $array['from'];
						$message = $array['message'];
						$attachments = $array['attachments'];
						$complex = $array['complex'];
						$id = $array['id'];
						$unread = $array['unread'];
						$sentfrom = $array['sent_from'];
						$plugin = $array['pluginname'];
						
						// Make "plugin-readable" variables
						if($complex == 1){
							$complex = true;
						}else{
							$complex = false;
						}
						
						// Generate
						$mailMess = array();
						$mailMess["id"] = $id;
						$mailMess["to"] = $to;
						$mailMess["from"] = $from;
						$mailMess["message"] = $message;
						$mailMess["complex"] = $complex;
						$mailMess["attachments"] = $attachments;
						$mailMess["unread"] = $unread;
						$mailMess["sentfrom"] = $sentfrom;
						$mailMess["pluginOwner"] = $plugin;
						echo "\n".json_encode($mailMess);
					}
				}else{
					echo json_encode(array("message" => "no mail", "status" => "OK"));
				}
			}else{
				echo json_encode(array("message" => "Incorrect username or password", "status" => "ERROR", "username" => $username, "loggedin" => false));
			}
		}else{
			echo json_encode(array("message" => "Unknown arguments", "status" => "ERROR", "mode" => $mode));
		}
	}else if($mode == "READ"){
		$username = clean($_POST['username']);
		// Check API key
		check_key($ip, $mode, $key, $username);
		if(valid($username)){
			$query = mysql_query("SELECT id FROM users WHERE username='$username' AND loggedin='1'") or die(mysql_error());
			if(mysql_num_rows($query)==1 || strpos($username, 'CONSOLE@') !== false){
				$query = mysql_query("SELECT * FROM `mail` WHERE `to`='$username' AND `unread`='0'") or die(mysql_error());
				if(mysql_num_rows($query)>0){
					echo json_encode(array("message" => "read", "status" => "OK", "username" => $username, "read" => mysql_num_rows($query)));
					while($array = mysql_fetch_array($query)){
						$to = $array['to'];
						$from = $array['from'];
						$message = $array['message'];
						$attachments = $array['attachments'];
						$complex = $array['complex'];
						$id = $array['id'];
						$unread = $array['unread'];
						$sentfrom = $array['sent_from'];
						$plugin = $array['pluginname'];
						
						// Make "plugin-readable" variables
						if($complex == 1){
							$complex = true;
						}else{
							$complex = false;
						}
						
						// Generate
						$mailMess = array();
						$mailMess["id"] = $id;
						$mailMess["to"] = $to;
						$mailMess["from"] = $from;
						$mailMess["message"] = $message;
						$mailMess["complex"] = $complex;
						$mailMess["attachments"] = $attachments;
						$mailMess["unread"] = $unread;
						$mailMess["sentfrom"] = $sentfrom;
						$mailMess["pluginOwner"] = $plugin;
						echo "\n".json_encode($mailMess);
					}
				}else{
					echo json_encode(array("message" => "no mail", "status" => "OK"));
				}
			}else{
				echo json_encode(array("message" => "Incorrect username or password", "status" => "ERROR", "username" => $username, "loggedin" => false));
			}
		}else{
			echo json_encode(array("message" => "Unknown arguments", "status" => "ERROR", "mode" => $mode));
		}
	}else if($mode == "INFO"){
		echo json_encode(array("message" => "xMail PHP Server", "status" => "OK", "version" => "XMAIL-CUSTOM_SERVER", "posturl" => "http://xmail.turt2live.com/mail", "ip" => $ip, "now" => $now, "timezone" => $config['timezone'], "hasNoLogin" => $config["settings.nologin"]));
	}else if($mode == "SETTINGS"){
		if(valid($_POST['username'])){
			$username = clean($_POST['username']);
			$settings = array(
				"welcomeMessage" => true,
				"longWelcomeMessage" => true,
				"newMessage" => true,
				"longNewMessage" => true,
				"mailboxLoading" => true,
				"sentMessage" => true,
				"loginMessage" => true,
				"longLoginMessage" => true,
				"folderLoadingMessage" => true,
				"messageLoadingMessage" => true,
				"loginStatusMessage" => true,
				"markingAsReadMessage" => true,
				"massSendStatusMessage" => true,
				"alwaysAskMarkAsRead" => true,
				"alwaysMarkAsRead" => true,
				"username" => $username,
				"message" => "Settings for $username",
				"status" => "OK"
			);
			$query = mysql_query("SELECT * FROM usersettings WHERE username='$username'");
			if(mysql_num_rows($query)>0){
				if(mysql_result($query, 0, "showWelcomeMessage")=="CHECKED"){ $settings['welcomeMessage']=true; }else{ $settings['welcomeMessage']=false; }
				if(mysql_result($query, 0, "showLongWelcomeMessage")=="CHECKED"){ $settings['longWelcomeMessage']=true; }else{ $settings['longWelcomeMessage']=false; }
				if(mysql_result($query, 0, "showNewMessageAlert")=="CHECKED"){ $settings['newMessage']=true; }else{ $settings['newMessage']=false; }
				if(mysql_result($query, 0, "showLongNewMessageAlert")=="CHECKED"){ $settings['longNewMessage']=true; }else{ $settings['longNewMessage']=false; }
				if(mysql_result($query, 0, "showMailboxLoadMessage")=="CHECKED"){ $settings['mailboxLoading']=true; }else{ $settings['mailboxLoading']=false; }
				if(mysql_result($query, 0, "showSentMessage")=="CHECKED"){ $settings['sentMessage']=true; }else{ $settings['sentMessage']=false; }
				if(mysql_result($query, 0, "showLoginMessage")=="CHECKED"){ $settings['loginMessage']=true; }else{ $settings['loginMessage']=false; }
				if(mysql_result($query, 0, "showLongLoginMessage")=="CHECKED"){ $settings['longLoginMessage']=true; }else{ $settings['longLoginMessage']=false; }
				if(mysql_result($query, 0, "showFolderLoadingMessage")=="CHECKED"){ $settings['folderLoadingMessage']=true; }else{ $settings['folderLoadingMessage']=false; }
				if(mysql_result($query, 0, "showMessageLoadinMessage")=="CHECKED"){ $settings['messageLoadingMessage']=true; }else{ $settings['messageLoadingMessage']=false; }
				if(mysql_result($query, 0, "showLoginStatusMessage")=="CHECKED"){ $settings['loginStatusMessage']=true; }else{ $settings['loginStatusMessage']=false; }
				if(mysql_result($query, 0, "showMarkingAsReadMessage")=="CHECKED"){ $settings['markingAsReadMessage']=true; }else{ $settings['markingAsReadMessage']=false; }
				if(mysql_result($query, 0, "showMassSendStatusMessage")=="CHECKED"){ $settings['massSendStatusMessage']=true; }else{ $settings['massSendStatusMessage']=false; }
				if(mysql_result($query, 0, "alwaysAskToMarkAsRead")=="CHECKED"){ $settings['alwaysAskMarkAsRead']=true; }else{ $settings['alwaysAskMarkAsRead']=false; }
				if(mysql_result($query, 0, "alwaysMarkAsRead")=="CHECKED"){ $settings['alwaysMarkAsRead']=true; }else{ $settings['alwaysMarkAsRead']=false; }
			}
			echo json_encode($settings);
		}else{
			echo json_encode(array("message" => "Invalid arguments", "status" => "ERROR", "mode" => $mode));
		}
    }else if($mode == "AUTH_INFO"){
			echo json_encode(array("message" => "Feature not supported", "status" => "OK"));
	}else{
		echo json_encode(array("message" => "Invalid Mode", "status" => "ERROR"));
	}
}
?>
<?php
/*
 * xMail core server
 * 
 * Here is where everything is passed in from the plugin.
 */

// includes
require_once "includes/constants.inc.php";
require_once "includes/ConfigHandler.inc.php";
require_once "includes/misc.inc.php";
require_once "includes/keys.inc.php";

// Construct some classes before we get moving
$config = configHandler::singleton();

// You will need to change these values, most likely
mysql_connect($config->get("mysql.server"), $config->get("mysql.username"), $config->get("mysql.password")) or die(mysql_error());
mysql_select_db($config->get("mysql.database")) or die(mysql_error());


// Passed in from the plugin (POST)
$mode = clean($_POST['mode']); // Mode selection
$key = clean($_POST['apikey']); // API Key (if needed)
$ip = $_SERVER['REMOTE_ADDR']; // IP, for internal use
$version = clean($_POST['version']); // Version, just in case
$now = time(); // Used internally 
if(!valid($mode)){ // Check for "invalid" setting of the mode
	// NOTE: All of xMail is JSON based
	echo json_encode(array("message" => "Invalid Mode", "status" => "ERROR"));
}else{	
	// Special case for importing from another mail plugin
	// The server sends back a ONE TIME use key
	if($mode == "IMPORT_KEY"){
		$key = sha1("import".time());
		mysql_query("INSERT INTO `import_keys` (`key`, `ip`) VALUES ('$key', '$ip')") or die(mysql_error());
		die(json_encode(array("message" => $key, "status" => "OK")));
	}
	// Send Mail
	if($mode == "SEND"){
		// All variables are cleaned before use
		$pid = clean($_POST['pid']); // "Post ID", aka: Message ID (not used)
		$uid = clean($_POST['uid']); // Unique ID (not used)
		$ident = clean($_POST['ident']); // Message identity, can be "S" for simple or "C" for complex
		$to = clean($_POST['to']); // Send to
		$from = clean($_POST['from']); // Sent from
		$message = clean($_POST['message']); // Message contents
		$attachments = ""; // Default blank
		// Check API key
		check_key($ip, $mode, $key, $from); // Will kill the connection if invalid
		if(valid($pid) && valid($uid) && valid($ident) && valid($to) && valid($from) && valid($message)){
			if($ident == "S"){
				// Simple mail: Just insert and tell the plugin we're good
				mysql_query("INSERT INTO `mail` (`to`, `from`, `message`, `unread`, `complex`, `sent`) VALUES ('$to', '$from', '$message', '1', '0', '$now')") or die(mysql_error());
				echo json_encode(array("message" => "Message sent!", "status" => "OK"));
			}else if($ident == "C"){
				// Complex mail: Remove attachments from message, and insert
				$message = str_replace("&amp;", "&", $message); // Rebuild message
				$parts = explode(";", $message); // Split out attachments, this needs to be safer (TODO)
				$message = $parts[0]; // Message
				$attachments = ""; 
				// Rebuild attachments
				for($i=1;$i<count($parts);$i++){
					$attachments .= $parts[$i].";";
				}
				// Insert and return
				mysql_query("INSERT INTO `mail` (`to`, `from`, `message`, `unread`, `complex`, `attachments`, `sent`) VALUES ('$to', '$from', '$message', '1', '1', '$attachments', '$now')") or die(mysql_error());
				echo json_encode(array("message" => "Message sent!", "status" => "OK"));
			}else{
				echo json_encode(array("message" => "Unknown ident", "status" => "ERROR"));
			}
		}else{
			echo json_encode(array("message" => "Unknown arguments", "status" => "ERROR", "mode" => $mode));
		}
	}else if($mode == "MARK"){
		// Mark mail as read or unread
		$read = clean($_POST['read']); // Read state
		$pid = clean($_POST['pid']); // Post ID
		$uid = clean($_POST['uid']); // Unique ID (not used)
		$from = clean($_POST['username']); // not used
		// Check API key
		check_key($ip, $mode, $key, $from);
		if(valid($read) && valid($pid) && valid($uid)){
			// Read is used as 'unread', so inverse
			if($read == true){
				$read = 0;
			}else{
				$read = 1;
			}
			// Update and alert plugin
			mysql_query("UPDATE mail SET unread='$read' WHERE id='$pid' LIMIT 1") or die(mysql_error());
			echo json_encode(array("message" => "Updated Message", "status" => "OK"));
		}else{
			echo json_encode(array("message" => "Unknown arguments", "status" => "ERROR", "mode" => $mode));
		}
	}else if($mode == "REGISTER"){
		// Registers a new user
		$username = clean($_POST['username']);
		$password = clean($_POST['password']); // Encoded by plugin
		if(valid($username) && valid($password)){
			$query = mysql_query("SELECT id FROM users WHERE username='$username'") or die(mysql_error());
			if(mysql_num_rows($query)==1){
				echo json_encode(array("message" => "Username in use", "status" => "ERROR"));
			}else{
				$key = sha1($now);
				mysql_query("INSERT INTO users (username, password, loggedin, lastlogin, apikey) VALUES ('$username', '$password', '1', '$now', '$key')") or die(mysql_error());
				$key = get_key($ip, $mode, $username);
				echo json_encode(array("message" => "User registered", "status" => "OK", "username" => $username, "date" => $now, "loggedin" => true, "lastlogin" => $now, "apikey" => $key));
			}
		}else{
			echo json_encode(array("message" => "Unknown arguments", "status" => "ERROR", "mode" => $mode));
		}
	}else if($mode == "LOGIN"){
		// Login as a user
		$username = clean($_POST['username']);
		$password = clean($_POST['password']); // Encoded by plugin
		if(valid($username) && valid($password)){
			$query = mysql_query("SELECT id,lastlogin FROM users WHERE username='$username' AND password='$password'") or die(mysql_error());
			if(mysql_num_rows($query)==1){
				$last = mysql_result($query, 0, "lastlogin");
				mysql_query("UPDATE users SET loggedin='1', lastlogin='$now' WHERE username='$username' LIMIT 1") or die(mysql_error());
				$key = get_key($ip, $mode, $username);
				echo json_encode(array("message" => "Logged in", "status" => "OK", "username" => $username, "loggedin" => true, "date" => $now, "lastlogin" => $last, "apikey" => $key));
			}else{
				echo json_encode(array("message" => "Incorrect username or password", "status" => "ERROR", "username" => $username, "loggedin" => false));
			}
		}else{
			echo json_encode(array("message" => "Unknown arguments", "status" => "ERROR", "mode" => $mode));
		}
	}else if($mode == "LOGOUT"){
		// Logout 
		$username = clean($_POST['username']);
		// Check API key
		check_key($ip, $mode, $key, $username);
		if(valid($username)){
			mysql_query("UPDATE users SET loggedin='0' WHERE username='$username' LIMIT 1") or die(mysql_error());
			destroy_key($ip, $mode, $username);
			echo json_encode(array("message" => "Logged out", "status" => "OK", "username" => $username));
		}else{
			echo json_encode(array("message" => "Unknown arguments", "status" => "ERROR", "mode" => $mode));
		}
	}else if($mode == "CHECK_LOGIN"){
		// "Auth Check", this is called by the plugin every so often to verify that the player is still logged in
		$username = clean($_POST['username']);
		// Check API key
		check_key($ip, $mode, $key, $username);
		if(strpos($username, 'CONSOLE@') !== false){
			die(json_encode(array("message" => "Logged in", "status" => "OK", "username" => $username, "loggedin" => true, "date" => $now, "lastlogin" => $now, "apikey" => null)));
		}
		if(valid($username)){
			$query = mysql_query("SELECT id,lastlogin FROM users WHERE username='$username' AND loggedin='1'") or die(mysql_error());
			if(mysql_num_rows($query)==1){
				$last = mysql_result($query, 0, "lastlogin");
				mysql_query("UPDATE users SET loggedin='1', lastlogin='$now' WHERE username='$username' LIMIT 1") or die(mysql_error());
				$key = get_key($ip, $mode, $username);
				echo json_encode(array("message" => "Logged in", "status" => "OK", "username" => $username, "loggedin" => true, "date" => $now, "lastlogin" => $last, "apikey" => $key));
			}else{
				echo json_encode(array("message" => "Incorrect username or password", "status" => "ERROR", "username" => $username, "loggedin" => false));
			}
		}else{
			echo json_encode(array("message" => "Unknown arguments", "status" => "ERROR", "mode" => $mode));
		}
	}else if($mode == "INBOX"){
		// Fetches the inbox
		$username = clean($_POST['username']);
		// Check API key
		check_key($ip, $mode, $key, $username);
		if(valid($username)){
			// Check login
			$query = mysql_query("SELECT id FROM users WHERE username='$username' AND loggedin='1'") or die(mysql_error());
			if(mysql_num_rows($query)==1 || strpos($username, 'CONSOLE@') !== false){ // Verify inbox
				$query = mysql_query("SELECT * FROM `mail` WHERE `to`='$username' AND `unread`='1'") or die(mysql_error());
				if(mysql_num_rows($query)>0){
					// Spit out basic details
					echo json_encode(array("message" => "inbox", "status" => "OK", "username" => $username, "unread" => mysql_num_rows($query)));
					while($array = mysql_fetch_array($query)){
						// Gather information
						$to = $array['to'];
						$from = $array['from'];
						$message = $array['message'];
						$attachments = $array['attachments'];
						$complex = $array['complex'];
						$id = $array['id'];
						
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
						echo "\n".json_encode($mailMess);
						/*
						The new line is required because the plugin reads the first line as
						the "basic info" line and any lines afterwards as "mail".
						*/
					}
				}else{
					// No mail
					echo json_encode(array("message" => "no mail", "status" => "OK"));
				}
			}else{
				echo json_encode(array("message" => "Incorrect username or password", "status" => "ERROR", "username" => $username, "loggedin" => false));
			}
		}else{
			echo json_encode(array("message" => "Unknown arguments", "status" => "ERROR", "mode" => $mode));
		}
	}else{
		echo json_encode(array("message" => "Invalid Mode", "status" => "ERROR"));
	}
}
?>
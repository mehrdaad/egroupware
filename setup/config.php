<?php
  /**************************************************************************\
  * phpGroupWare - Setup                                                     *
  * http://www.phpgroupware.org                                              *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */

  $phpgw_info["flags"] = array("noheader"   => True,   "nonavbar" => True,
  							 "currentapp" => "home", "noapi"    => True);
  include("../header.inc.php");

  $phpgw_info["server"]["api_dir"] = $phpgw_info["server"]["include_root"]."/phpgwapi";
 
  // Authorize the user to use setup app
  include("./inc/setup_auth.inc.php");
  // Does not return unless user is authorized
  
  /* Database setup */
  include($phpgw_info["server"]["api_dir"] . "/phpgw_db_".$phpgw_info["server"]["db_type"].".inc.php");

  $db	          = new db;
  $db->Host	    = $phpgw_info["server"]["db_host"];
  $db->Type	    = $phpgw_info["server"]["db_type"];
  $db->Database    = $phpgw_info["server"]["db_name"];
  $db->User	    = $phpgw_info["server"]["db_user"];
  $db->Password    = $phpgw_info["server"]["db_pass"];

  if ($newsettings["auth_type"] != "ldap") {
     echo "<title>phpGroupWare - setup</title>";
     echo '<body bgcolor="FFFFFF">';
     echo "<center>phpGroupWare version " . $phpgw_info["server"]["version"] . " setup</center><p>";
  }

  if ($submit) {
     @$db->query("delete from config");
     while ($newsetting = each($newsettings)) {
   	if ($newsetting[0] == "nntp_server") {
 	     $db->query("select config_value FROM config WHERE config_name='nntp_server'");
	      if ($db->num_rows()) {
	         $db->next_record();
  	       if ($db->f("config_value") <> $newsetting[1]) {
	            $db->query("DELETE FROM newsgroups");
   	         $db->query("DELETE FROM users_newsgroups");
   	      }
	      }
   	}
       $db->query("insert into config (config_name, config_value) values ('" . addslashes($newsetting[0])
    	  	  . "','" . addslashes($newsetting[1]) . "')");
     }
     if ($newsettings["auth_type"] == "ldap") {
        Header("Location: ldap.php");
        exit;
     } else {
        echo "<center>Your config has been updated<br><a href='".$newsettings["webserver_url"]."/login.php'>Click here to login</a>";
     }
  }

  @$db->query("select * from config");
  while (@$db->next_record()) {
    $current_config[$db->f("config_name")] = $db->f("config_value");
  }
?>  
 <form method="POST" action="config.php">
  <table border="0" align="center">
   <tr bgcolor="486591">
    <td colspan="2"><font color="fefefe">&nbsp;<b>Directory information</b></font></td>
   </tr>
   
   </tr>
   <tr bgcolor="e6e6e6">
    <td>Enter path for temporary files.</td>
    <td><input name="newsettings[temp_dir]" value="<?php echo $current_config["temp_dir"]; ?>" size="40"></td>
   </tr>

   <tr bgcolor="e6e6e6">
    <td>Enter path for users and group files.</td>
    <td><input name="newsettings[files_dir]" value="<?php echo $current_config["files_dir"]; ?>" size="40"></td>
   </tr>
   
   <tr bgcolor="e6e6e6">
    <td>Enter the location of phpGroupWare's URL.<br>Example: /phpGroupWare<br>(leave blank if at http://yourserver/)</td>
    <td><input name="newsettings[webserver_url]" value="<?php echo $current_config["webserver_url"]; ?>"></td>
   </tr>

   <tr bgcolor="FFFFFF">
    <td colspan="2">&nbsp;</td>
   </tr>

   <tr bgcolor="486591">
    <td colspan="2"><font color="fefefe">&nbsp;<b>Mail settings</b></font></td>
   </tr>

   <tr bgcolor="e6e6e6">
    <td>Enter the location of your mail server:</td>
    <td><input name="newsettings[mail_server]" value="<?php echo $current_config["mail_server"]; ?>"></td>
   </tr>

   <?php $selected[$current_config["mail_server_type"]] = " selected"; ?>
   <tr bgcolor="e6e6e6">
    <td>Select your mail server type:</td>
    <td>
     <select name="newsettings[mail_server_type]">
      <option value="imap"<?php echo $selected["imap"]; ?>>IMAP</option>
      <option value="pop3"<?php echo $selected["pop3"]; ?>>POP-3</option>
     </select>
    </td>
   </tr>
   <?php $selected = array(); ?>

   <?php $selected[$current_config["imap_server_type"]] = " selected"; ?>
   <tr bgcolor="e6e6e6">
    <td>IMAP server type:</td>
    <td>
     <select name="newsettings[imap_server_type]">
      <option value="Cyrus"<?php echo $selected["Cyrus"]; ?>>Cyrus</option>
      <option value="UWash"<?php echo $selected["UWash"]; ?>>UWash</option>
     </select>
    </td>
   </tr>
   <?php $selected = array(); ?>

   <tr bgcolor="e6e6e6">
    <td>Enter your mail sufix:</td>
    <td><input name="newsettings[mail_suffix]" value="<?php echo $current_config["mail_suffix"]; ?>"></td>
   </tr>

   <?php $selected[$current_config["mail_login_type"]] = " selected"; ?>
   <tr bgcolor="e6e6e6">
    <td>Mail server login type:</td>
    <td>
     <select name="newsettings[mail_login_type]">
      <option value="standard"<?php echo $selected["standard"]; ?>>standard</option>
      <option value="vmailmgr"<?php echo $selected["vmailmgr"]; ?>>vmailmgr</option>
     </select>
    </td>
   </tr>
   <?php $selected = array(); ?>

   <tr bgcolor="e6e6e6">
    <td>Enter your SMTP server hostname:</td>
    <td><input name="newsettings[smtp_server]" value="<?php echo $current_config["smtp_server"]; ?>"></td>
   </tr>

   <tr bgcolor="e6e6e6">
    <td>Enter your SMTP server port:</td>
    <td><input name="newsettings[smtp_port]" value="<?php echo $current_config["smtp_port"]; ?>"></td>
   </tr>

   <tr bgcolor="FFFFFF">
    <td colspan="2">&nbsp;</td>
   </tr>

   <tr bgcolor="486591">
    <td colspan="2"><font color="fefefe">&nbsp;<b>Authentication</b></font></td>
   </tr>

   <?php $selected[$current_config["auth_type"]] = " selected"; ?>
   <tr bgcolor="e6e6e6">
    <td>Select which type of authentication you are using.</td>
    <td>
     <select name="newsettings[auth_type]">
      <option value="sql"<?php echo $selected["sql"]; ?>>SQL</option>
      <option value="ldap"<?php echo $selected["ldap"]; ?>>LDAP</option>
     </select>
    </td>
   </tr>
   <?php $selected = array(); ?>

   <tr bgcolor="e6e6e6">
    <td>LDAP host:</td>
    <td><input name="newsettings[ldap_host]" value="<?php echo $current_config["ldap_host"]; ?>"></td>
   </tr>

   <tr bgcolor="e6e6e6">
    <td>LDAP context:</td>
    <td><input name="newsettings[ldap_context]" value="<?php echo $current_config["ldap_context"]; ?>" size="40"></td>
   </tr>

   <tr bgcolor="e6e6e6">
    <td>LDAP root dn:</td>
    <td><input name="newsettings[ldap_root_dn]" value="<?php echo $current_config["ldap_root_dn"]; ?>" size="40"></td>
   </tr>

   <tr bgcolor="e6e6e6">
    <td>LDAP root password:</td>
    <td><input name="newsettings[ldap_root_pw]" value="<?php echo $current_config["ldap_root_pw"]; ?>"></td>
   </tr>
   
   <?php $selected[$current_config["ldap_encryption_type"]] = " selected"; ?>
   <tr bgcolor="e6e6e6">
    <td>LDAP encryption type</td>
    <td>
     <select name="newsettings[ldap_encryption_type]">
      <option value="DES"<?php echo $selected["DES"]; ?>>DES</option>
      <option value="MD5"<?php echo $selected["MD5"]; ?>>MD5</option>
     </select>
    </td>
   </tr>
   <?php $selected = array(); ?>

   <tr bgcolor="e6e6e6">
    <td>Use cookies to pass sessionid:</td>
    <td><input type="checkbox" name="newsettings[usecookies]" value="True"<?php echo ($current_config["usecookies"]?" checked":""); ?>></td>
   </tr>
   
   <tr bgcolor="e6e6e6">
    <td>Enter some random text for app_session <br>encryption (requires mcrypt)</td>
    <td><input name="newsettings[encryptkey]" value="<?php echo $current_config["encryptkey"]; ?>" size="40"></td>
   </tr>
   
   <tr bgcolor="FFFFFF">
    <td colspan="2">&nbsp;</td>
   </tr>

   <tr bgcolor="486591">
    <td colspan="2"><font color="fefefe">&nbsp;<b>Server information</b></font></td>
   </tr>

   <tr bgcolor="e6e6e6">
    <td>Enter your default FTP server.</td>
    <td><input name="newsettings[default_ftp_server]" value="<?php echo $current_config["default_ftp_server"]; ?>"></td>
   </tr>

   <tr bgcolor="e6e6e6">
    <td>Enter your HTTP proxy server.</td>
    <td><input name="newsettings[httpproxy_server]" value="<?php echo $current_config["httpproxy_server"]; ?>"></td>
   </tr>

   <tr bgcolor="e6e6e6">
    <td>Enter your HTTP proxy server port.</td>
    <td><input name="newsettings[httpproxy_port]" value="<?php echo $current_config["httpproxy_port"]; ?>"></td>
   </tr>

   <?php $selected[$current_config["showpoweredbyon"]] = " selected"; ?>
   <tr bgcolor="e6e6e6">
    <td>Showed powered by logo on:</td>
    <td>
     <select name="newsettings[showpoweredbyon]">
      <option value="bottom"<?php echo $selected["bottom"]; ?>>bottom</option>
      <option value="top"<?php echo $selected["top"]; ?>>top</option>
     </select>
    </td>
   </tr>
   <?php $selected = array(); ?>

   <tr bgcolor="e6e6e6">
    <td>Would like like phpGroupWare to check for new version<br>when admins login ?:</td>
    <td><input type="checkbox" name="newsettings[checkfornewversion]" value="True"<?php echo ($current_config["checkfornewversion"]?" checked":""); ?>></td>
   </tr>

   <tr bgcolor="e6e6e6">
    <td>Enter the title for your site.</td>
    <td><input name="newsettings[site_title]" value="<?php echo $current_config["site_title"]; ?>"></td>
   </tr>

   <tr bgcolor="e6e6e6">
    <td>Enter the hostname of the machine this server is running on.</td>
    <td><input name="newsettings[hostname]" value="<?php echo $SERVER_NAME; ?>"></td>
   </tr>

   <tr bgcolor="FFFFFF">
    <td colspan="2">&nbsp;</td>
   </tr>

   <tr bgcolor="486591">
    <td colspan="2"><font color="fefefe">&nbsp;<b>NNTP settings</b></font></td>
   </tr>
   
   <tr bgcolor="e6e6e6">
    <td>Enter your NNTP server hostname:</td>
    <td><input name="newsettings[nntp_server]" value="<?php echo $current_config["nntp_server"]; ?>"></td>
   </tr>

   <tr bgcolor="e6e6e6">
    <td>Enter your NNTP server port:</td>
    <td><input name="newsettings[nntp_port]" value="<?php echo $current_config["nntp_port"]; ?>"></td>
   </tr>

   <tr bgcolor="e6e6e6">
    <td>Enter your NNTP sender:</td>
    <td><input name="newsettings[nntp_sender]" value="<?php echo $current_config["nntp_sender"]; ?>"></td>
   </tr>

   <tr bgcolor="e6e6e6">
    <td>Enter your NNTP organization:</td>
    <td><input name="newsettings[nntp_organization]" value="<?php echo $current_config["nntp_organization"]; ?>"></td>
   </tr>

   <tr bgcolor="e6e6e6">
    <td>Enter your NNTP admins email address:</td>
    <td><input name="newsettings[nntp_admin]" value="<?php echo $current_config["nntp_admin"]; ?>"></td>
   </tr>

   <tr bgcolor="e6e6e6">
    <td>Enter your NNTP login:</td>
    <td><input name="newsettings[nntp_login_username]" value="<?php echo $current_config["nntp_login_username"]; ?>"></td>
   </tr>

   <tr bgcolor="e6e6e6">
    <td>Enter your NNTP password:</td>
    <td><input name="newsettings[nntp_login_password]" value="<?php echo $current_config["nntp_login_password"]; ?>"></td>
   </tr>

   <tr bgcolor="FFFFFF">
    <td colspan="2">&nbsp;</td>
   </tr>

   <tr>
    <td colspan="2" align="center"><input type="submit" name="submit" value="Submit"></td>
   </tr>
 </table>
</form>
</body></html>

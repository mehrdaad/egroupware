<?php
/**************************************************************************\
* phpGroupWare - Address Book                                              *
* http://www.phpgroupware.org                                              *
* --------------------------------------------                             *
*  This program is free software; you can redistribute it and/or modify it *
*  under the terms of the GNU General Public License as published by the   *
*  Free Software Foundation; either version 2 of the License, or (at your  *
*  option) any later version.                                              *
\**************************************************************************/

/* $Id$ */

	$phpgw_info["flags"] = array(
		"noheader" => True, 
		"nonavbar" => True, 
		"currentapp" => "addressbook", 
		"enable_contacts_class" => True,
		"enable_nextmatchs_class" => True
	);
                               
	include("../header.inc.php");

	$this = CreateObject("phpgwapi.contacts");

 	$extrafields = array(
		"ophone"   => "ophone",
		"address2" => "address2",
		"address3" => "address3"
	);

	$phpgw->preferences->read_repository();
	$customfields = array();
	if ($phpgw_info["user"]["preferences"]["addressbook"]) {
		while (list($col,$descr) = each($phpgw_info["user"]["preferences"]["addressbook"])) {
			if ( substr($col,0,6) == 'extra_' ) {
				$field = ereg_replace('extra_','',$col);
				$customfields[$field] = ucfirst($field);
			}
		}
	}

	$qfields = $this->stock_contact_fields + $extrafields + $customfields;

	if ($submit) {
		$totalerrors = 0;
		if (! count($ab_selected)) {
			$errors[$totalerrors++] = lang("You must select at least 1 column to display");
		}
		if (! $totalerrors) {
			$phpgw->preferences->read_repository();
			while (list($pref[0]) = each($qfields)) {
				if ($ab_selected["$pref[0]"]) {
					$phpgw->preferences->change("addressbook",$pref[0],"addressbook_" . $ab_selected["$pref[0]"]);
				} else {
					$phpgw->preferences->delete("addressbook",$pref[0],"addressbook_" . $ab_selected["$pref[0]"]);
				}
			}

 			if ($mainscreen_showbirthdays) {
				$phpgw->preferences->delete("addressbook","mainscreen_showbirthdays");
				$phpgw->preferences->add("addressbook","mainscreen_showbirthdays");
			} else {
				$phpgw->preferences->delete("addressbook","mainscreen_showbirthdays");
			}

 			if ($autosave_category) {
				$phpgw->preferences->delete("addressbook","autosave_category");
				$phpgw->preferences->add("addressbook","autosave_category",True);
			} else {
				$phpgw->preferences->delete("addressbook","autosave_category");
			}

 			if ($cat_id) {
				$phpgw->preferences->delete("addressbook","default_category");
				$phpgw->preferences->add("addressbook","default_category",$cat_id);
			} else {
				$phpgw->preferences->delete("addressbook","default_category");
			}

			$phpgw->preferences->save_repository(True);
			Header("Location: " . $phpgw->link("/preferences/index.php"));
		}
	}

	$phpgw->common->phpgw_header();
	echo parse_navbar();

	if ($totalerrors) {  
		echo "<p><center>" . $phpgw->common->error_list($errors) . "</center>";
	}

	echo "<p><b>" . lang("Addressbook preferences") . ":" . "</b><hr><p>";
?>
  <form method="POST" action="<?php echo $phpgw->link('/addressbook/preferences.php'); ?>">
   <table border="0" align="center" cellspacing="1" cellpadding="1">
    <?php
	// I need to create a common function to handle displaying multiable columns
    
	echo "<tr bgcolor=\"" . $phpgw_info["theme"]["th_bg"] . "\"><td colspan=\"3\">&nbsp;</td></tr>\n";
	$i = 0; $j = 0;
	$tr_color = $phpgw->nextmatchs->alternate_row_color($tr_color);
	echo "<tr bgcolor=\"" . $tr_color . "\">\n";
	while (list($col, $descr) = each($qfields)) {
		// echo "<br>test: $col - $i $j - " . count($abc);
		$i++; $j++;
		$showcol = display_name($col);
		if (!$showcol) { $showcol = $col; }
		// yank the *'s prior to testing for a valid column description
		$coltest = ereg_replace("\*","",$showcol);
		if ($coltest) {
			echo "\t<td><input type=\"checkbox\" name=\"ab_selected[" . $col . "]\" value=\"True\""
			. ($phpgw_info["user"]["preferences"]["addressbook"][$col]?" checked":"") . '>' . $showcol
			. "</option></td>\n";
		} else {
			$i--;
			next;
		}
		if ($i == 3) {
			echo "</tr>\n";
			$i = 0;
		}
		if ($i == 0 && $coltest) {
			$tr_color = $phpgw->nextmatchs->alternate_row_color($tr_color);
			echo "<tr bgcolor=\"" . $tr_color . "\">\n";
		}
		if ($j == count($qfields)) {
			if ($i == 1) {
				echo "\t<td>&nbsp;</td><td>&nbsp;</td>\n";
			}
			if ($i == 2) {
				echo "\t<td>&nbsp;</td>\n";
			}
			echo "</tr>\n";
		}
	}
	$tr_color = $phpgw->nextmatchs->alternate_row_color($tr_color);
    ?>
    <tr bgcolor="<?php echo $tr_color; ?>">
     <td colspan="2"><?php echo lang("show birthday reminders on main screen"); ?></td>
     <td><input type="checkbox" name="mainscreen_showbirthdays" value="true"<?php if ($phpgw_info["user"]["preferences"]["addressbook"]["mainscreen_showbirthdays"]) echo " checked"; ?>></td>
    </tr><?php
	$tr_color = $phpgw->nextmatchs->alternate_row_color($tr_color);?>
    <tr bgcolor="<?php echo $tr_color; ?>">
     <td colspan="2"><?php echo lang("Autosave default category"); ?></td>
     <td><input type="checkbox" name="autosave_category" value="true"<?php if ($phpgw_info["user"]["preferences"]["addressbook"]["autosave_category"]) echo " checked"; ?>></td>
    </tr><?php
	$tr_color = $phpgw->nextmatchs->alternate_row_color($tr_color);?>
    <tr bgcolor="<?php echo $tr_color; ?>">
     <td colspan="2"><?php echo lang("Default Category"); ?></td>
     <td><? echo cat_option($phpgw_info["user"]["preferences"]["addressbook"]["default_category"]); ?></td>
    </tr>
    <tr>
     <td colspan="3" align="center">
      <input type="submit" name="submit" value="<?php echo lang("submit"); ?>">
     </td>
    </tr>
   </table>
  </form>
<?php
	$phpgw->common->phpgw_footer();
?>

<?php
  /**************************************************************************\
  * phpGroupWare - addressbook                                               *
  * http://www.phpgroupware.org                                              *
  * Written by Joseph Engo <jengo@mail.com>                                  *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */

	class soaddressbook
	{
		var $contacts;
		var $rights;

		function soaddressbook()
		{
			global $phpgw,$rights;

			$phpgw->contacts   = CreateObject('phpgwapi.contacts');

			$this->contacts = &$phpgw->contacts;
			$this->rights   = $rights;
		}

		function read_entries($start,$offset,$qcols,$query,$qfilter,$sort,$order)
		{
			$readrights = $this->rights & PHPGW_ACL_READ;
			return $this->contacts->read($start,$offset,$qcols,$query,$qfilter,$sort,$order,$readrights);
		}

		function read_entry($id,$fields)
		{
			if ($this->rights & PHPGW_ACL_READ)
			{
				return $this->contacts->read_single_entry($id,$fields);
			}
			else
			{
				$rtrn = array('No access' => 'No access');
				return $rtrn;
			}
		}

		function read_last_entry($fields)
		{
			if ($this->rights & PHPGW_ACL_READ)
			{
				return $this->contacts->read_last_entry($fields);
			}
			else
			{
				$rtrn = array('No access' => 'No access');
				return $rtrn;
			}
		}

		function add_entry($userid,$fields)
		{
			if ($this->rights & PHPGW_ACL_ADD)
			{
				$this->contacts->add($userid,$fields,$fields['access'],$fields['cat_id'],$fields['tid']);
			}
			return;
		}

		function get_lastid()
		{
		 	$entry = $this->contacts->read_last_entry();
			$ab_id = $entry[0]['id'];
			return $ab_id;
		}

		function update_entry($userid,$fields)
		{
			if ($this->rights & PHPGW_ACL_EDIT)
			{
				$this->contacts->update($fields['ab_id'],$userid,$fields,$fields['access'],$fields['cat_id']);
			}
			return;
		}
	}
?>

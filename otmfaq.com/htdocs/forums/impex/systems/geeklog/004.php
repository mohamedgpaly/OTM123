<?php if (!defined('IDIR')) { die; }
/*======================================================================*\
|| #################################################################### ||
|| # vBulletin  - Licence Number VBF98A5CB5
|| # ---------------------------------------------------------------- # ||
|| # All PHP code in this file is �2000-2006 Jelsoft Enterprises Ltd. # ||
|| # This file may not be redistributed in whole or significant part. # ||
|| # ---------------- VBULLETIN IS NOT FREE SOFTWARE ---------------- # ||
|| # http://www.vbulletin.com | http://www.vbulletin.com/license.html # ||
|| #################################################################### ||
\*======================================================================*/
/**
* geeklog_004 Import User module
*
* @package			ImpEx.geeklog
* @version			$Revision: 1.4 $
* @author			Jerry Hutchings <jerry.hutchings@vbulletin.com>
* @checkedout		$Name:  $
* @date				$Date: 2006/04/03 08:46:42 $
* @copyright		http://www.vbulletin.com/license.html
*
*/
class geeklog_004 extends geeklog_000
{
	var $_version 		= '0.0.1';
	var $_dependent 	= '003';
	var $_modulestring 	= 'Import User';


	function geeklog_004()
	{
		// Constructor
	}


	function init(&$sessionobject, &$displayobject, &$Db_target, &$Db_source)
	{
		if ($this->check_order($sessionobject,$this->_dependent))
		{
			if ($this->_restart)
			{
				if ($this->restart($sessionobject, $displayobject, $Db_target, $Db_source,'clear_imported_users'))
				{
					$displayobject->display_now('<h4>Imported users have been cleared</h4>');
					$this->_restart = true;
				}
				else
				{
					$sessionobject->add_error('fatal',
											 $this->_modulestring,
											 get_class($this) . '::restart failed , clear_imported_users','Check database permissions');
				}
			}


			// Start up the table
			$displayobject->update_basic('title','Import User');
			$displayobject->update_html($displayobject->do_form_header('index',substr(get_class($this) , -3)));
			$displayobject->update_html($displayobject->make_hidden_code(substr(get_class($this) , -3),'WORKING'));
			$displayobject->update_html($displayobject->make_hidden_code('import_user','working'));
			$displayobject->update_html($displayobject->make_table_header($this->_modulestring));


			// Ask some questions
			$displayobject->update_html($displayobject->make_input_code('Users to import per cycle (must be greater than 1)','userperpage',500));
			$displayobject->update_html($displayobject->make_yesno_code("Would you like to associated imported users with existing users if the email address matches ?","email_match",0));


			// End the table
			$displayobject->update_html($displayobject->do_form_footer('Continue','Reset'));


			// Reset/Setup counters for this
			$sessionobject->add_session_var(substr(get_class($this) , -3) . '_objects_done', '0');
			$sessionobject->add_session_var(substr(get_class($this) , -3) . '_objects_failed', '0');
			$sessionobject->add_session_var('userstartat','0');
			$sessionobject->add_session_var('userdone','0');


			$tdt = $sessionobject->get_session_var('targetdatabasetype');
			$ttp = $sessionobject->get_session_var('targettableprefix');

			$this->add_custom_field($Db_target, $tdt, $ttp, 'user_name','The user name');

		}
		else
		{
			// Dependant has not been run
			$displayobject->update_html($displayobject->do_form_header('index',''));
			$displayobject->update_html($displayobject->make_description('<p>This module is dependent on <i><b>' . $sessionobject->get_module_title($this->_dependent) . '</b></i> cannot run until that is complete.'));
			$displayobject->update_html($displayobject->do_form_footer('Continue',''));
			$sessionobject->set_session_var(substr(get_class($this) , -3),'FALSE');
			$sessionobject->set_session_var('module','000');
		}
	}


	function resume(&$sessionobject, &$displayobject, &$Db_target, &$Db_source)
	{
		// Set up working variables.
		$displayobject->update_basic('displaymodules','FALSE');
		$target_database_type	= $sessionobject->get_session_var('targetdatabasetype');
		$target_table_prefix	= $sessionobject->get_session_var('targettableprefix');
		$source_database_type	= $sessionobject->get_session_var('sourcedatabasetype');
		$source_table_prefix	= $sessionobject->get_session_var('sourcetableprefix');


		// Per page vars
		$user_start_at			= $sessionobject->get_session_var('userstartat');
		$user_per_page			= $sessionobject->get_session_var('userperpage');
		$class_num				= substr(get_class($this) , -3);


		// Start the timing
		if(!$sessionobject->get_session_var($class_num . '_start'))
		{
			$sessionobject->timing($class_num ,'start' ,$sessionobject->get_session_var('autosubmit'));
		}


		// Get an array of user details
		$user_array 	= $this->get_geeklog_user_details($Db_source, $source_database_type, $source_table_prefix, $user_start_at, $user_per_page);

		$user_group_ids_array = $this->get_imported_group_ids($Db_target, $target_database_type, $target_table_prefix);

		// Display count and pass time
		$displayobject->display_now('<h4>Importing ' . count($user_array) . ' users</h4><p><b>From</b> : ' . $user_start_at . ' ::  <b>To</b> : ' . ($user_start_at + count($user_array)) . '</p>');


		$user_object = new ImpExData($Db_target, $sessionobject, 'user');


		foreach ($user_array as $user_id => $user_details)
		{
			$try = (phpversion() < '5' ? $user_object : clone($user_object));

			if ($user_details['username'] == 'Anonymous')
			{
				$displayobject->display_now('<br />Guest user, skipping');
				continue;
			}

			// Auto associate
			if ($sessionobject->get_session_var('email_match'))
			{
				$try->_auto_email_associate = true;
			}

			$old_user_group = $this->get_geek_usersgroups($Db_source, $source_database_type, $source_table_prefix, $user_id);

			// Mandatory
			if (count($old_user_group))
			{
				$try->set_value('mandatory', 'usergroupid',			$user_group_ids_array[69]);
			}
			else
			{
				echo "<h1>'" . $old_user_group[0] . "'</h1>";
				echo "<h1>'" . $user_group_ids_array[$old_user_group[0]] . "'</h1>";
				$try->set_value('mandatory', 'usergroupid',			$user_group_ids_array[$old_user_group[0]]);
			}

			// Looks like the moderator dosn't have one.
			$try->set_value('mandatory', 'username',				$user_details['username']);
			$try->set_value('mandatory', 'email',					$user_details['email']);
			$try->set_value('mandatory', 'importuserid',			$user_id);

			// Non Mandatory
			if (count($old_user_group) > 1)
			{
				unset($new_grouparray);
				foreach($old_user_group as $id => $old_group)
				{
					$new_grouparray[] = $user_group_ids_array[$old_group];
				}
				$try->set_value('nonmandatory', 'membergroupids',	implode(',',$new_grouparray));
			}


			$try->_password_md5_already = true;
			$try->set_value('nonmandatory', 'password',				$user_details['passwd']);
			$try->set_value('nonmandatory', 'options',				'2135');
			$try->set_value('nonmandatory', 'joindate',				strtotime($user_details['regdate']));
			$try->set_value('nonmandatory', 'lastvisit',			$user_details['lastlogin']);

			if ($user_details['bio'])
			{
				$try->add_default_value('Biography', 				substr(addslashes($user_details['bio']), 0, 250));
			}

			if ($user_details['homepage'])
			{
				$try->set_value('nonmandatory', 'homepage',			$user_details['homepage']);
			}

			if ($user_details['sig'])
			{
				$try->add_default_value('signature', 				addslashes($this->html_2_bb($user_details['sig'])));
			}

			if($user_details['full_name'])
			{
				$try->add_custom_value('user_name', 				$user_details['full_name']);
			}

			/*
			$try->set_value('nonmandatory', 'displaygroupid',		$user_details['displaygroupid']);
			$try->set_value('nonmandatory', 'passworddate',			$user_details['passworddate']);
			$try->set_value('nonmandatory', 'styleid',				$user_details['styleid']);
			$try->set_value('nonmandatory', 'parentemail',			$user_details['parentemail']);
			$try->set_value('nonmandatory', 'homepage',				$user_details['homepage']);
			$try->set_value('nonmandatory', 'icq',					$user_details['icq']);
			$try->set_value('nonmandatory', 'aim',					$user_details['aim']);
			$try->set_value('nonmandatory', 'yahoo',				$user_details['yahoo']);
			$try->set_value('nonmandatory', 'showvbcode',			$user_details['showvbcode']);
			$try->set_value('nonmandatory', 'usertitle',			$user_details['usertitle']);
			$try->set_value('nonmandatory', 'customtitle',			$user_details['customtitle']);
			$try->set_value('nonmandatory', 'daysprune',			$user_details['daysprune']);
			$try->set_value('nonmandatory', 'lastactivity',			$user_details['lastactivity']);
			$try->set_value('nonmandatory', 'lastpost',				$user_details['lastpost']);
			$try->set_value('nonmandatory', 'posts',				$user_details['posts']);
			$try->set_value('nonmandatory', 'reputation',			$user_details['reputation']);
			$try->set_value('nonmandatory', 'reputationlevelid',	$user_details['reputationlevelid']);
			$try->set_value('nonmandatory', 'timezoneoffset',		$user_details['timezoneoffset']);
			$try->set_value('nonmandatory', 'pmpopup',				$user_details['pmpopup']);
			$try->set_value('nonmandatory', 'avatarid',				$user_details['avatarid']);
			$try->set_value('nonmandatory', 'avatarrevision',		$user_details['avatarrevision']);
			$try->set_value('nonmandatory', 'birthday',				$user_details['birthday']);
			$try->set_value('nonmandatory', 'maxposts',				$user_details['maxposts']);
			$try->set_value('nonmandatory', 'startofweek',			$user_details['startofweek']);
			$try->set_value('nonmandatory', 'ipaddress',			$user_details['ipaddress']);
			$try->set_value('nonmandatory', 'referrerid',			$user_details['referrerid']);
			$try->set_value('nonmandatory', 'languageid',			$user_details['languageid']);
			$try->set_value('nonmandatory', 'msn',					$user_details['msn']);
			$try->set_value('nonmandatory', 'emailstamp',			$user_details['emailstamp']);
			$try->set_value('nonmandatory', 'threadedmode',			$user_details['threadedmode']);
			$try->set_value('nonmandatory', 'pmtotal',				$user_details['pmtotal']);
			$try->set_value('nonmandatory', 'pmunread',				$user_details['pmunread']);
			$try->set_value('nonmandatory', 'autosubscribe',		$user_details['autosubscribe']);
			$try->set_value('nonmandatory', 'avatar',				$user_details['avatar']);
			$try->set_value('nonmandatory', 'birthday_search',		$user_details['birthday_search']);
			*/

			// Check if user object is valid
			if($try->is_valid())
			{
				if($try->import_user($Db_target, $target_database_type, $target_table_prefix))
				{
					$displayobject->display_now('<br /><span class="isucc"><b>' . $try->how_complete() . '%</b></span> :: user -> ' . $user_details['username']);
					$sessionobject->add_session_var($class_num . '_objects_done',intval($sessionobject->get_session_var($class_num . '_objects_done')) + 1 );
				}
				else
				{
					$sessionobject->set_session_var($class_num . '_objects_failed',$sessionobject->get_session_var($class_num. '_objects_failed') + 1 );
					$sessionobject->add_error('warning', $this->_modulestring, get_class($this) . '::import_custom_profile_pic failed.', 'Check database permissions and database table');
					$displayobject->display_now("<br />Found avatar user and <b>DID NOT</b> imported to the  {$target_database_type} database");
				}
			}
			else
			{
				$displayobject->display_now("<br />Invalid user object, skipping." . $try->_failedon);
			}
			unset($try);
		}// End foreach


		// Check for page end
		if (count($user_array) == 0 OR count($user_array) < $user_per_page)
		{
			$sessionobject->timing($class_num,'stop', $sessionobject->get_session_var('autosubmit'));
			$sessionobject->remove_session_var($class_num . '_start');


			$this->build_user_statistics($Db_target, $target_database_type, $target_table_prefix);


			$displayobject->update_html($displayobject->module_finished($this->_modulestring,
										$sessionobject->return_stats($class_num, '_time_taken'),
										$sessionobject->return_stats($class_num, '_objects_done'),
										$sessionobject->return_stats($class_num, '_objects_failed')
										));


			$sessionobject->set_session_var($class_num ,'FINISHED');
			$sessionobject->set_session_var('import_user','done');
			$sessionobject->set_session_var('module','000');
			$sessionobject->set_session_var('autosubmit','0');
			$displayobject->update_html($displayobject->print_redirect('index.php','1'));
		}


		$sessionobject->set_session_var('userstartat',$user_start_at+$user_per_page);
		$displayobject->update_html($displayobject->print_redirect('index.php'));
	}// End resume
}//End Class
# Autogenerated on : December 3, 2004, 2:46 pm
# By ImpEx-generator 1.4.
/*======================================================================*\
|| ####################################################################
|| # Downloaded: 03:45, Mon Nov 13th 2006
|| # CVS: $RCSfile: 004.php,v $ - $Revision: 1.4 $
|| ####################################################################
\*======================================================================*/
?>

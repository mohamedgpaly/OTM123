<?php
/*======================================================================*\
|| #################################################################### ||
|| # vBulletin 4.1.1 Patch Level 1 - Licence Number VBS309B87F
|| # ---------------------------------------------------------------- # ||
|| # Copyright �2000-2011 vBulletin Solutions Inc. All Rights Reserved. ||
|| # This file may not be redistributed in whole or significant part. # ||
|| # ---------------- VBULLETIN IS NOT FREE SOFTWARE ---------------- # ||
|| # http://www.vbulletin.com | http://www.vbulletin.com/license.html # ||
|| #################################################################### ||
\*======================================================================*/
if (!VB_API) die;

$VB_API_WHITELIST = array(
	'response' => array(
		'HTML' => array(
			'buddycount',
			'buddylist' => array(
				'*' => array(
					'container', 'friendcheck_checked',
					'user' => array(
						'userid', 'usertitle', 'avatarurl', 'avatarwidth', 'avatarheight',
						'username', 'type', 'checked'
					),
					'show' => array(
						'incomingrequest', 'outgoingrequest', 'friend_checkbox'
					)
				)
			),
			'buddy_username',
			'incominglist' => array(
				'*' => array(
					'container', 'friendcheck_checked',
					'user' => array(
						'userid', 'usertitle', 'avatarurl', 'avatarwidth', 'avatarheight',
						'username', 'type', 'checked'
					),
					'show' => array(
						'incomingrequest', 'outgoingrequest', 'friend_checkbox'
					)
				)
			),
			'perpage', 'pagenumber', 'pagenav'
		)
	)
);

/*======================================================================*\
|| ####################################################################
|| # Downloaded: 17:08, Tue Jul 12th 2011
|| # CVS: $RCSfile$ - $Revision: 35584 $
|| ####################################################################
\*======================================================================*/
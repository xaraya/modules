<?php

function xproject_adminapi_menu()
{
    $menu = array();

	$dateformatlist = array('Please choose a Date/Time Format',
							'%m/%d/%Y',
							'%B %d, %Y',
							'%a, %B %d, %Y',
							'%A, %B %d, %Y',
							'%m/%d/%Y %H:%M',
							'%B %d, %Y %H:%M',
							'%a, %B %d, %Y %H:%M',
							'%A, %B %d, %Y %H:%M',
							'%m/%d/%Y %I:%M %p',
							'%B %d, %Y %I:%M %p',
							'%a, %B %d, %Y %I:%M %p',
							'%A, %B %d, %Y %I:%M %p');
	$menu['dateformatlist'] = $dateformatlist;

    $menu['menutitle'] = xarML('XProject Administration');

    $menu['menulabel_new'] = xarML('New Project');
    $menu['menulabel_view'] = xarML('Projects');
    $menu['menulabel_search'] = xarML('Search');
    $menu['menulabel_config'] = xarML('Config');

    $menu['status'] = '';

    return $menu;
}
?>

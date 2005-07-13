<?php

/**
 * viewallgroups - generate all groups listing.
 * @param none
 * @return groups listing of available groups
 */
function xproject_groupsapi_viewallgroups()
{
    if (!xarModAPILoad('groups', 'user')) {
	    $groups = xarModAPIFunc('xproject','groups','getall');
    } else {
	    $groups = xarModAPIFunc('groups','user','getall');
	}

    return $groups;
}


/*
 * deletegroup - delete a group & info
 * @param $args['gid']
 * @return true on success, false otherwise
 */
?>
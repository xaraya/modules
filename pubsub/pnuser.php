<?php
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------

/**
 * the main user function
 */
function pubsub_user_main()
{
    // Return output
    return pnML('This module has no user interface *except* via display hooks');
}

/**
 * display pubsub element next to a registered event
 * @param $args['objectid'] ID of the item this rating is for
 * @param $args['extrainfo'] URL to return to if user chooses to rate
 * @returns output
 * @return output with rating information
 */
function pubsub_user_display($args)
{
// This function will display the output code next to an item that has a 
// registered pubsub event associated with it.
// It will display an icon to subscribe to the event if the user is registered
// if they arent then it will display nothing.
// If they are logged in and have already subscribed it will display an
// unsubscribe icon.

    extract($args);

    // Load API
    if (!pnModAPILoad('pubsub', 'user')) {
        $msg = pnML('API Failed to Load',
	            'pubsub');
	pnExceptionSet(PN_SYSTEM_EXCEPTION,
	            'LOAD_FAILED',
	            new SystemException($msg));
	return;
    }

    // TODO: make it actually do what the comment says it will :-)
    return $data;
}

?>

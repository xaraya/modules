<?php
// ----------------------------------------------------------------------
// Xaraya Content Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.com/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------
// Original Author of file: Chris Dudley
// Purpose of file:  Pubsub user display functions
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

    // Create new output object
    $output = new pnHTML();
     
    extract($args);

    // Load API
    if (!pnModAPILoad('pubsub', 'user')) {
        $output->Text(_LOADFAILED);
        return $output->GetOutput();
    }

    // TODO: make it actually do what the comment says it will :-)
    $output->Text('<IMG SRC=modules/pubsub/pnimages/subscribe.png">');
    return $output->GetOutput();
}

?>

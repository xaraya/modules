<?php
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file:
// Purpose of file: Show Pinups of Coolpick.com
// ----------------------------------------------------------------------


/**
 * the main user function
 */
function pinup_user_main()
{
    $url = 'http://www.coolpick.com/img/syn/_babe/' . date('Ymd') . '.jpg';
    return array('piclocation' => $url);
}
?>
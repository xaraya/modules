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
    $url    = "http://coolpick.com/way/cool/syn/babe.html";
    $needle = '7babpol.shtml"\n><img src="';

    $lines_array = file($url);
    $lines = implode('', $lines_array);

    $match_flg = preg_match("°".$needle."([^\"]+)(\")°", $lines, $matches);

    return array('piclocation' => $matches[1]);
}
?>
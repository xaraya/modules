<?php
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Frank Besler <besfred@xaraya.com>
// Purpose of file: Show Image feeds
// ----------------------------------------------------------------------


/**
 * the main user function
 */
function imagefeed_user_main()
{

	// basic data
    $url    = "http://coolpick.com/way/cool/syn/babe.html";
    $needle = '7babpol.shtml"\n><img src="';

	// get actual site sources
    $lines_array = file($url);
    $lines = implode('', $lines_array);

	// retriev the image location
    $match_flg = preg_match("°".$needle."([^\"]+)(\")°", $lines, $matches);

    return array('piclocation' => $matches[1],
    			 'link' => 'http://coolpick.com/way/cool/syn/babe.html',
    			 'title' => 'CoolPick.com');
}
?>
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
    $location = '';
    $url = "http://coolpick.com/way/cool/syn/babe.html";
    $lines_array = file($url);
    $lines_string = implode('', $lines_array);
    eregi("7babpol.shtml(.*)<TD ALIGN=center VALIGN=center>", $lines_string, $head);
    $lines = split("\n", $head[0]);
    $x = count($lines);
    for ($i=0;$i<$x;$i++) {
        $again=eregi_replace("</td>", " ", $lines[$i]);
        $again=eregi_replace("7babpol.shtml", " ", $again);
//      $again=eregi_replace("\" >", " ", $again);
        $location .= $again;
    }
    return array('piclocation' => $location);
}
?>
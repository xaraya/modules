<?php
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Frank Besler <besfred@xaraya.com>
// Purpose of file: Initialize Image Feed Module
// ----------------------------------------------------------------------

/**
 * initialise the image feed module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function imagefeed_init()
{
/*  // no longer supported
    xarModSetVar('imagefeed', 'url', 'http://coolpick.com/way/cool/syn/babe.html');
    xarModSetVar('imagefeed', 'find', '7babpol.shtml"\n><img src="([^"]+)"');
    xarModSetVar('imagefeed', 'prefix', '');
    xarModSetVar('imagefeed', 'title', 'Babe of the Day');
    xarModSetVar('imagefeed', 'descr', 'CoolPick.com');
    xarModSetVar('imagefeed', 'link', 'http://coolpick.com/way/cool/syn/babe.html');
    xarModSetVar('imagefeed', 'refresh', 24*60*60);
*/

    // sample configuration
    xarModSetVar('imagefeed', 'url', 'http://mikespub.net/pictures/daily.php');
    xarModSetVar('imagefeed', 'find', '\t\t<img SRC="([^"]+)"');
    xarModSetVar('imagefeed', 'prefix', 'http://mikespub.net');
    xarModSetVar('imagefeed', 'title', 'Daily Picture at Mike\'s Pub');
    xarModSetVar('imagefeed', 'descr', '<a href="/pictures/daily.php\?id=\d+">(.*?)</a>');
    xarModSetVar('imagefeed', 'link', '<a href="(/pictures/daily.php\?id=\d+)"');
    xarModSetVar('imagefeed', 'refresh', 24*60*60);

    return true;
}

/**
 * delete the image feed module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function imagefeed_delete()
{
    return true;
}
?>

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
    $url     = xarModGetVar('imagefeed', 'url');
    $find    = xarModGetVar('imagefeed', 'find');
    $prefix  = xarModGetVar('imagefeed', 'prefix');
    $title   = xarModGetVar('imagefeed', 'title');
    $descr   = xarModGetVar('imagefeed', 'descr');
    $link    = xarModGetVar('imagefeed', 'link');
    $refresh = xarModGetVar('imagefeed', 'refresh');

    // get actual site source
    $lines = xarModAPIFunc('base', 'user', 'getfile',
                           array('url'       => $url,
                                 'cached'    => true,
                                 'cachedir'  => 'cache/rss',
                                 'refresh'   => $refresh,
                                 'extension' => '.html'));
    if (empty($lines)) return;

    // retrieve the image location
    if (preg_match("°".$find."°i", $lines, $matches)) {
        $image = $matches[1];
        if (!empty($prefix) && !stristr($image,$prefix)) {
            $image = $prefix . $image;
        }
    } else {
        $image = '';
    }

    // retrieve the link if it's variable
    if (strstr($link,'(')) {
        if (preg_match("°".$link."°i", $lines, $matches)) {
            $link = $matches[1];
            if (!empty($prefix) && !stristr($link,$prefix)) {
                $link = $prefix . $link;
            }
        } else {
            $link = $url;
        }
    }

    // retrieve the description if it's variable
    if (strstr($descr,'(')) {
        if (preg_match("°".$descr."°i", $lines, $matches)) {
            $descr = $matches[1];
        } else {
            $descr = $title;
        }
    }

    // prepare template variables
    $data = array('piclocation' => $image,
                  'title' => xarVarPrepForDisplay($title),
                  'descr' => xarVarPrepHTMLDisplay($descr),
                  'link' => $link);

    // only 1 itemid here (for now) !
    $data['module'] = 'imagefeed';
    $data['itemtype'] = 0;
    $data['itemid'] = 1;
    $data['returnurl'] = xarModURL('imagefeed','user','main');
    $data['hookoutput'] = xarModCallHooks('item', 'display', 1, $data);

    return $data;
}
?>

<?php

/**
 * File: $Id$
 *
 * Admin menu links for xmlrpcsystemapi
 *
 * @package modules
 * @copyright (C) 2004 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage xmlrpcsystemapi
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function xmlrpcsystemapi_adminapi_getmenulinks()
{

    $menulinks[] = Array('url'   => xarModURL('xmlrpcsystemapi','admin','introspect'),
                         'title' => xarML('Use introspection'),
                         'label' => xarML('Use introspection'));
    return $menulinks;
}
?>
<?php

/**
 * File: $Id$
 *
 * Function to retrieve itemtypes for bkview module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

include_once("modules/bkview/xarincludes/bk.class.php");

function bkview_userapi_getitemtypes() 
{
    $itemtypes = array();
    $itemtypes[BK_ITEMTYPE_REPO] = array('label' => xarVarPrepForDisplay('Repositories'),
                          'title' => xarVarPrepForDisplay('Repository information'),
                          'url'   => xarModUrl('bkview','user','view')
                          );
    $itemtypes[BK_ITEMTYPE_FILE] = array('label' => xarVarPrepForDisplay('Files'),
                          'title' => xarVarPrepForDisplay('File contents'),
                          'url'   => xarModUrl('bkview','user','view')
                          );
    $itemtypes[BK_ITEMTYPE_CSET] = array('label' => xarVarPrepForDisplay('Changesets'),
                          'title' => xarVarPrepForDisplay('Changeset comments'),
                          'url'   => xarModUrl('bkview','user','view')
                          );
    $itemtypes[BK_ITEMTYPE_DELTA] = array('label' => xarVarPrepForDisplay('Deltas'),
                          'title' => xarVarPrepForDisplay('Delta comments'),
                          'url'   => xarModUrl('bkview','user','view')
                          );

    return $itemtypes;
}
?>
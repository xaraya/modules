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

function bkview_userapi_getitemtypes() 
{
    $itemtypes = array();
    $itemtypes[1] = array('label' => xarVarPrepForDisplay('Repositories'),
                          'title' => xarVarPrepForDisplay('Repositories'),
                          'url'   => xarModUrl('bkview','user','view')
                          );
    $itemtypes[2] = array('label' => xarVarPrepForDisplay('Changesets'),
                          'title' => xarVarPrepForDisplay('Changesets'),
                          'url'   => xarModUrl('bkview','user','view')
                          );
    $itemtypes[3] = array('label' => xarVarPrepForDisplay('Deltas'),
                          'title' => xarVarPrepForDisplay('Deltas'),
                          'url'   => xarModUrl('bkview','user','view')
                          );
    $itemtypes[4] = array('label' => xarVarPrepForDisplay('Files'),
                          'title' => xarVarPrepForDisplay('Files'),
                          'url'   => xarModUrl('bkview','user','view')
                          );

    return $itemtypes;
}
?>
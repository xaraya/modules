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
    $itemtypes[BK_ITEMTYPE_REPO] = array('label' => xarML('Repositories'),
                                         'title' => xarML('Repository information'),
                                         'url'   => xarModUrl('bkview','user','view')
                                         );
    $itemtypes[BK_ITEMTYPE_FILE] = array('label' => xarML('Files'),
                                         'title' => xarML('File contents'),
                                         'url'   => xarModUrl('bkview','user','view')
                                         );
    $itemtypes[BK_ITEMTYPE_CSET] = array('label' => xarVarPrepForDisplay('Changesets'),
                                         'title' => xarML('Changeset comments'),
                                         'url'   => xarModUrl('bkview','user','view')
                                         );
    $itemtypes[BK_ITEMTYPE_DELTA] = array('label' => xarML('Deltas'),
                                          'title' => xarML('Delta comments'),
                                          'url'   => xarModUrl('bkview','user','view')
                                          );
    
    ksort($itemtypes);
    
    return $itemtypes;
}
?>
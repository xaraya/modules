<?php
/**
 * Utility function to retrieve the list of item types
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/**
 * Utility function to retrieve the list of item types of this module 
 *
 * @author jojodee
 * @return array containing the item types and their description
 */
function legis_userapi_getitemtypes($args)
{
    $itemtypes = array();
    $mastertypes = xarModAPIFunc('legis','user','getmastertypes');

    foreach ($mastertypes as $id => $mastertype) {

        $itemtypes[$id] = array('label' => xarVarPrepForDisplay($mastertype['mdname']),
                                'title' => xarVarPrepForDisplay(xarML('Display #(1)',$mastertype['mdname'])),
                                'url'   => xarModURL('legis','user','view',array('mdid' => $mastertype['mdid']))
                               );
    }
    return $itemtypes;
}
?>

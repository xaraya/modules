<?php
/**
 * File: $Id:
 * 
 * Utility function to retrieve the list of item types of this module
 * 
  * @copyright (C) 2004 by Jo Dalle Nogare
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage xarcpshop
 * @author jojodee@xaraya.com
 */
/**
 * utility function to retrieve the list of item types of this module (if any)
 *
 * @returns array
 * @return array containing the item types and their description
 */
function xarcpshop_userapi_getitemtypes($args)
{
    $itemtypes = array();

    $itemtypes[1] = array('label' => xarVarPrepForDisplay(xarML('xarCPShops')),
                          'title' => xarVarPrepForDisplay(xarML('View xarCPShops')),
                          'url'   => xarModURL('xarcpshop','user','view'));
    return $itemtypes;
}

?>

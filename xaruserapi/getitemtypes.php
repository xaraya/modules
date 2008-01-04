<?php
/**
 * Utility function to retrieve the list of item types
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Utility function to retrieve the list of item types of this module (if any)
 *
 * Itemtypes are the planitems with their ids.
 * To allow for hooks to full ITSPs and Plans we have added the itemtype 99998 and 99999.
 *
 * @author MichelV <michelv@xarayahosting.nl>
 *
 * @return array containing the item types and their description
 * @todo MichelV <1> decide on the setup in here
 */
function itsp_userapi_getitemtypes($args)
{
    $itemtypes = array();
    // Get the plans
    $pitems = xarModApiFunc('itsp','user','getall_planitems');
    if (count($pitems) > 0) {
        foreach ($pitems as $pitem) {
            $id = $pitem['pitemid'];
            $itemtypes[$id] = array('label' => xarVarPrepForDisplay($pitem['pitemname']),
                                    'title' => xarVarPrepForDisplay(xarML('Display #(1)',$pitem['pitemname'])),
                                    'url'   => xarModURL('itsp','user','display',array('pitemid' => $id))
                                   );
        }
    }
    /*  Add the plans and the seperate ITSPs to the array */
    $itemtypes[99998] = array('label' => xarVarPrepForDisplay(xarML('ITSP Plans')),
                      'title' => xarVarPrepForDisplay(xarML('View ITSP Plans')),
                      'url'   => xarModURL('itsp','user','view'));

    $itemtypes[99999] = array('label' => xarVarPrepForDisplay(xarML('ITSPs')),
                      'title' => xarVarPrepForDisplay(xarML('View ITSPs')),
                      'url'   => xarModURL('itsp','user','viewitsp'));

    return $itemtypes;
}
?>
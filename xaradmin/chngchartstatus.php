<?php
/**
 * Update an item
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data Example Module
 * @link http://xaraya.com/index.php/release/66.html
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * update an item
 */
function labAccounting_admin_chngchartstatus($args)
{
    if(!xarVarFetch('chartid',   'id', $chartid)) {return;}
    if(!xarVarFetch('active', 'int', $active,  NULL, XARVAR_DONT_SET)) {return;}
    
    extract($args);

    if (empty($chartid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'chart account id', 'admin', 'chngchartstatus', 'labAccounting');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $object = xarModAPIFunc('dynamicdata','user','getobject',
                             array('module' => 'labAccounting',
                                    'itemtype' => 5,
                                    'itemid' => $chartid));
    if (!isset($object)) return;

    // get the values for this item
    $newid = $object->getItem();
    if (!isset($newid) || $newid != $chartid) return;
    
    $object->properties['active']->value = $active;
//echo "<pre>"; print_r($object); die("</pre>");

    // check the input values for this object
//    $isvalid = $object->checkInput();
    
//    if(!$isvalid) return;

    // update the item
    $chartid = $object->updateItem();

    if (empty($chartid)) return; // throw back

    // let's go back to the admin view
    xarResponseRedirect(xarModURL('labaccounting', 'admin', 'view'));

    // Return
    return true;
}
?>

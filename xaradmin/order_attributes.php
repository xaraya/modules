<?php
/**
 * EAV Module
 *
 * @package modules
 * @subpackage eav
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2013 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Re-order the attributes of an object
 *
 * @param int objectid
 * @param int modid
 * @param int itemtype
 * @throws BAD_PARAM
 * @return boolean true on success and redirect to modifyprop
 */
function eav_admin_order_attributes()
{
    // Security
    if(!xarSecurityCheck('EditEAV')) return;

    // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVarFetch()
    if(!xarVarFetch('objectid',          'isset', $objectid,          NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('itemid',        'isset', $itemid,         NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('direction',     'isset', $direction,      NULL, XARVAR_DONT_SET)) {return;}

    if (empty($direction)) {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = array('direction', 'admin', 'orderprops', 'dynamicdata');
        throw new BadParameterException($vars,$msg);
    }

    if (empty($itemid)) {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = array('itemid', 'admin', 'orderprops', 'dynamicdata');
        throw new BadParameterException($vars,$msg);
    }

    if (!xarSecConfirmAuthKey()) {
        //return xarTpl::module('privileges','user','errors',array('layout' => 'bad_author'));
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $object = DataObjectMaster::getObject(array('objectid' => $objectid));
    $objectinfo = DataObjectMaster::getObjectInfo(
                                    array(
                                    'objectid' => $objectid,
                                    ));

    $objectid = $objectinfo['objectid'];

    $fields = xarMod::apiFunc('eav','user','getattributes',
                                   array('objectid' => $objectid,
                                         'allprops' => true));
    $orders = array();
    $currentpos = null;
    foreach ($fields as $fname => $field) {
        if ($field['id'] == $itemid) {
            $move_prop = $fname;
            $currentpos = $field['seq'];
        }
        $orders[] = $fname;
    }
    $i = 0;
    foreach ($fields as $name => $field) {
        if ($field['seq'] == $currentpos && $direction == 'up' && isset($orders[$i-1])) {
            $swapwith = $orders[$i-1];
            $swappos = $i;
            $currentpos = $i+1;
        } elseif ($field['seq'] == $currentpos && $direction == 'down' && isset($orders[$i+1])) {
            $swapwith = $orders[$i+1];
            $swappos = $i;
            $currentpos = $i+1;
        }
        if (isset($swappos)) break;
        $i++;
    }

    if (isset($swappos)) {
            $q = new Query('UPDATE', $tables['eav_attributes']);
            $q->addfield('seq', $fields[$swapwith]['seq']);
            $q->eq('id', $itemid);
            if(!$q->run()) return;

            $q = new Query('UPDATE', $tables['eav_attributes']);
            $q->addfield('seq', $fields[$swapwith]['seq']);
            $q->eq('id', $fields[$move_prop]['seq']);
            if(!$q->run()) return;
    }

    xarController::redirect(xarModURL('eav', 'admin', 'add_attribute',
                        array('objectid'    => $objectid,
        )));
    return true;
}

?>

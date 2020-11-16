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
    if (!xarSecurity::check('EditEAV')) {
        return;
    }

    // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVar::fetch()
    if (!xarVar::fetch('objectid', 'isset', $object_id, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('itemid', 'isset', $itemid, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('direction', 'isset', $direction, null, xarVar::DONT_SET)) {
        return;
    }

    if (empty($direction)) {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = array('direction', 'admin', 'orderprops', 'dynamicdata');
        throw new BadParameterException($vars, $msg);
    }

    if (empty($itemid)) {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = array('itemid', 'admin', 'orderprops', 'dynamicdata');
        throw new BadParameterException($vars, $msg);
    }

    $fields = xarMod::apiFunc(
        'eav',
        'user',
        'getattributes',
        array('object_id' => $object_id,
                                         'allprops' => true)
    );
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
        if (isset($swappos)) {
            break;
        }
        $i++;
    }

    sys::import('xaraya.structures.query');
    $tables =& xarDB::getTables();
    $q = new Query('UPDATE', $tables['eav_attributes']);
    $q->addfield('seq', (int)$fields[$swapwith]['seq']);
    $q->eq('id', (int)$itemid);
    if (!$q->run()) {
        return;
    }

    $q = new Query('UPDATE', $tables['eav_attributes']);
    $q->addfield('seq', (int)$fields[$move_prop]['seq']);
    $q->eq('id', (int)$fields[$swapwith]['seq']);
    if (!$q->run()) {
        return;
    }

    xarController::redirect(xarController::URL(
        'eav',
        'admin',
        'add_attribute',
        array('objectid'    => $object_id,
        )
    ));
    return true;
}

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
 * View items of the entities object
 *
 */
    function eav_admin_view_entities($args)
    {
        if (!xarSecurityCheck('ManageEAV')) return;

        $data['object'] = DataObjectMaster::getObjectList(array('name' => 'eav_entities'));

        if (!isset($data['object'])) {return;}
        if (!$data['object']->checkAccess('view'))
            return xarResponse::Forbidden(xarML('View #(1) is forbidden', $data['object']->label));

        // Count the number of items matching the preset arguments - do this before getItems()
        $data['object']->countItems();

        // Get the selected items using the preset arguments
        $data['object']->getItems();

        return $data;
    }
?>
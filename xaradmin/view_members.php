<?php
/**
 * Realms Module
 *
 * @package modules
 * @subpackage realms module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

/**
 * View users of each realm
 *
 */
    function realms_admin_view_members($args)
    {
        if (!xarSecurity::check('ManageRealms')) {
            return;
        }

        // Define which object will be shown
        if (!xarVar::fetch('realm_id', 'int', $data['realm_id'], 0, xarVar::DONT_SET)) {
            return;
        }

        // Get the available dropdown options
        $realms = DataObjectMaster::getObjectList(['name' => 'realms_realms']);
        $items = $realms->getItems();
        $options = [];
        foreach ($items as $item) {
            $options[] = ['id' => $item['id'], 'name' => $item['name']];
        }
        $data['options'] = $options;

        // Get the object containing the members
        $data['members'] = DataObjectMaster::getObjectList(['name' => 'realms_members']);

        // Kludge
        sys::import('xaraya.structures.query');
        $q = new Query('SELECT');
        $q->eq('members.state', 3);
        $q->eq('members.realm_id', $data['realm_id']);
        $data['conditions'] = $q;
        return $data;
    }

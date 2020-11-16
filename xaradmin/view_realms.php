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
 * View realms
 *
 */
    function realms_admin_view_realms($args)
    {
        if (!xarSecurity::check('ManageRealms')) {
            return;
        }

        // Get the object containing the members
        $data['realms'] = DataObjectMaster::getObjectList(array('name' => 'realms_realms'));

        // Kludge
        sys::import('xaraya.structures.query');
        $q = new Query('SELECT');
        $q->eq('realms.state', 3);
        $data['conditions'] = $q;
        return $data;
    }

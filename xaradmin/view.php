<?php
/**
 * Pubsub Module
 *
 * @package modules
 * @subpackage pubsub module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * View items of the gamer object
 *
 */
    function gamer_admin_view($args)
    {
        if (!xarSecurityCheck('ManageGamer')) return;

        $modulename = 'gamer';

        // Define which object will be shown
        if (!xarVarFetch('objectname', 'str', $objectname, null, XARVAR_DONT_SET)) return;
        if (!empty($objectname)) xarModUserVars::set($modulename,'defaultmastertable', $objectname);

        // Set a return url
        xarSession::setVar('ddcontext.' . $modulename, array('return_url' => xarServer::getCurrentURL()));

        // Get the available dropdown options
        $object = DataObjectMaster::getObjectList(array('objectid' => 1));
        $data['objectname'] = xarModUserVars::get($modulename,'defaultmastertable');
        $items = $object->getItems();
        $options = array();
        foreach ($items as $item)
            if (strpos($item['name'],$modulename) !== false)
                $options[] = array('id' => $item['name'], 'name' => $item['name']);
        $data['options'] = $options;
        return $data;
    }
?>
<?php
/**
 * Mime Module
 *
 * @package modules
 * @subpackage mime module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/999
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * View items of the mime objects
 *
 */
    function mime_admin_view($args)
    {
        if (!xarSecurity::check('ManageMime')) return;

        $modulename = 'mime';

        // Define which object will be shown
        if (!xarVar::fetch('objectname', 'str', $objectname, null, xarVar::DONT_SET)) return;
        if (!empty($objectname)) xarModUserVars::set($modulename,'defaultmastertable', $objectname);

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
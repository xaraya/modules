<?php
/**
 * Workflow Module
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Workflow Module
 * @link http://xaraya.com/index.php/release/188.html
 * @author Workflow Module Development Team
 */
/**
 * utility function to pass individual item links to whoever
 *
 * @param $args['itemtype'] item type (optional)
 * @param $args['itemids'] array of item ids to get
 * @return array containing the itemlink(s) for the item(s).
 */
function workflow_userapi_getitemlinks($args)
{
    extract($args);

    $itemlinks = array();
    if (empty($itemtype)) {
        return $itemlinks;
    }

    // Common setup for Galaxia environment
    sys::import('modules.workflow.lib.galaxia.config');
    include(GALAXIA_LIBRARY.'/gui.php');

    // get the instances this user has access to
    $sort = 'pId_asc, instanceId_asc';
    $find = '';
    $where = "gi.pId=$itemtype";
    $items = $GUI->gui_list_user_instances($user, 0, -1, $sort, $find, $where);

// TODO: add the instances you're the owner of (if relevant)

    if (empty($items['data']) || !is_array($items['data']) || count($items['data']) == 0) {
       return $itemlinks;
    }

    $itemid2key = array();
    foreach ($items['data'] as $key => $item) {
        $itemid2key[$item['instanceId']] = $key;
    }
    foreach ($itemids as $itemid) {
        if (!isset($itemid2key[$itemid])) continue;
        $item = $items['data'][$itemid2key[$itemid]];
        $itemlinks[$itemid] = array('url'   => xarModURL('workflow', 'user', 'instances',
                                                         array('filter_process' => $itemtype)),
                                    'title' => xarML('Display Instance'),
                                    'label' => xarVarPrepForDisplay($item['procname'] . ' ' . $item['version'] . ' # ' . $item['instanceId']));
    }
    return $itemlinks;
}

?>

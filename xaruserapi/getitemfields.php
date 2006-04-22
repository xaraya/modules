<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
/**
 * utility function to pass item field definitions to whoever
 *
 * @param $args['itemtype'] item type (optional)
 * @returns array
 * @return array containing the item field definitions
 */
function xarbb_userapi_getitemfields($args)
{
    extract($args);

    $itemfields = array();

    if (empty($itemtype)) {
        // forums
    } else {
        // topics
        $itemfields['ttitle']    = array('name'  => 'ttitle', 'label' => xarML('Subject'), 'type'  => 'textbox');
        $itemfields['tpost']     = array('name'  => 'tpost', 'label' => xarML('Body'), 'type'  => 'textarea_medium');
        $itemfields['tposter']   = array('name'  => 'tposter', 'label' => xarML('Author'), 'type'  => 'username');
        // Note: createtopic use ttime, updatetopic uses time :-(
        $itemfields['ttime']     = array('name'  => 'ttime', 'label' => xarML('Last Post'), 'type'  => 'calendar');
        $itemfields['tftime']    = array('name'  => 'tftime', 'label' => xarML('First Post'), 'type'  => 'calendar');
        $itemfields['treplies']  = array('name'  => 'treplies', 'label' => xarML('Replies'), 'type'  => 'numberbox');
        $itemfields['tstatus']   = array('name'  => 'tstatus', 'label' => xarML('Status'), 'type'  => 'numberbox');
        $itemfields['treplier']  = array('name'  => 'treplier', 'label' => xarML('Name'), 'type'  => 'username');
        $itemfields['thostname'] = array('name'  => 'thostname', 'label' => xarML('Hostname'), 'type'  => 'textbox');
    }

    return $itemfields;
}

?>
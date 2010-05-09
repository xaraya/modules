<?php

/**
 * File: $Id$
 *
 * Displays a crumb-trail block
 * Shows the visitor's current position in the page hierarchy
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2004 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage Xarpages Module
 * @author Jason Judge
*/
sys::import('modules.xarpages.xarblocks.crumb');

class Xarpages_CrumbBlockAdmin extends Xarpages_CrumbBlock implements iBlock
{
    /**
     * Modify Function to the Blocks Admin
     * @param $blockinfo array
     */
    public function modify(Array $data=array())
    {
        $data = parent::modify($data);
        if (empty($data)) return;

        // Defaults
        if (!isset($data['include_root'])) {$data['include_root'] = false;}
        if (!isset($data['root_pids'])) {$data['root_pids'] = array();}

        // Get a list of all pages for the drop-downs.
        // Get the tree of all pages, without the DD for speed.
        $data['all_pages'] = xarMod::apiFunc(
            'xarpages', 'user', 'getpagestree',
            array('dd_flag' => false, 'key' => 'pid')
        );

        // Implode the names for each page into a path for display.
        // TODO: move this into getpagestree
        foreach ($data['all_pages']['pages'] as $key => $page) {
            $data['all_pages']['pages'][$key]['slash_separated'] =  '/' . implode('/', $page['namepath']);
        }

        // Get the descriptions together for the current root pids.
        // TODO: we could prune the 'add root page' list so it only includes
        // the pages which are not yet under one of the selected root pages.
        // That would just be an extra little usability touch.
        $data['root_pids'] = array_flip($data['root_pids']);
        foreach($data['root_pids'] as $key => $value) {
            if (isset($data['all_pages']['pages'][$key])) {
                $data['root_pids'][$key] = $data['all_pages']['pages'][$key]['slash_separated'];
            } else {
                $data['root_pids'][$key] = xarML('Unknown');
            }
        }

        return $data;
    }
/**
 * Updates the Block config from the Blocks Admin
 * @param $blockinfo array containing title,content
 */
    public function update(Array $data=array())
    {
        $data = parent::update($data);
        // Reference to content array.
        $vars =& $data['content'];

        if (xarVarFetch('include_root', 'bool', $include_root, false, XARVAR_NOT_REQUIRED)) {
            $vars['include_root'] = $include_root;
        }

        // The root pages define sections of the page landscape that this block applies to.
        if (!isset($vars['root_pids'])) {
            $vars['root_pids'] = array();
        }
        if (xarVarFetch('new_root_pid', 'int:0', $new_root_pid, 0, XARVAR_NOT_REQUIRED) && !empty($new_root_pid)) {
            $vars['root_pids'][] = $new_root_pid;
        }
        if (xarVarFetch('remove_root_pid', 'list:int:1', $remove_root_pid, array(), XARVAR_NOT_REQUIRED) && !empty($remove_root_pid)) {
            // Easier to check with the keys and values flipped.
            $vars['root_pids'] = array_flip($vars['root_pids']);
            foreach($remove_root_pid as $remove) {
                if (isset($vars['root_pids'][$remove])) {
                    unset($vars['root_pids'][$remove]);
                }
            }
            // Flip keys and values back.
            $vars['root_pids'] = array_flip($vars['root_pids']);
            // Reorder the keys.
            $vars['root_pids'] = array_values($vars['root_pids']);
        }
        $data['content'] = $vars;

        return $data;

    }
}
?>
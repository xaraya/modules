<?php

/**
 * File: $Id$
 *
 * Displays a menu block
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2004 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage Xarpages Module
 * @author Jason Judge
*/

/**
 * Modify Function to the Blocks Admin
 * @param $blockinfo array (serialized or unserialized)
 */

    sys::import('modules.xarpages.xarblocks.menu');

    class Xarpages_MenuBlockAdmin extends Xarpages_MenuBlock implements iBlock
    {
/**
 * Modify Function to the Blocks Admin
 * @author Jason Judge
 * @param $blockinfo array containing title,content
 */

        public function modify()
        {
            $data = $this->getContent();

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

            $data['prune_pids'] = array_flip($data['prune_pids']);
            foreach($data['prune_pids'] as $key => $value) {
                if (isset($data['all_pages']['pages'][$key])) {
                    $data['prune_pids'][$key] = $data['all_pages']['pages'][$key]['slash_separated'];
                } else {
                    $data['prune_pids'][$key] = xarML('Unknown');
                }
            }

//            $vars['bid'] = $blockinfo['bid'];

            return $data;
        }

/**
 * Updates the Block config from the Blocks Admin
 * @param $blockinfo array containing title,content
 */
        public function update()
        {

            xarVarFetch('multi_homed', 'int', $vars['multi_homed'], 0, XARVAR_NOT_REQUIRED);

            // AUTO: the block picks up the page from cache Blocks.xarpages/current_pid.
            // DEFAULT: the block always uses the default page.
            // AUTODEFAULT: same as AUTO, but use the default page rather than NULL if outside and root page
            xarVarFetch('current_source', 'pre:upper:passthru:enum:AUTO:DEFAULT:AUTODEFAULT', $vars['current_source'], 'AUTO', XARVAR_NOT_REQUIRED);

            // The default page if none found by any other method.
            xarVarFetch('default_pid', 'int:0', $vars['default_pid'], 0, XARVAR_NOT_REQUIRED);

            // The root pages define sections of the page landscape that this block applies to.
            if (!isset($data['root_pids'])) $vars['root_pids'] = $this->root_pids;
            else $vars['root_pids'] = $data['root_pids'];

            xarVarFetch('new_root_pid', 'int:0', $new_root_pid, 0, XARVAR_NOT_REQUIRED) && !empty($new_root_pid);
            $vars['root_pids'][] = $new_root_pid;

            xarVarFetch('remove_root_pid', 'list:int:1', $remove_root_pid, array(), XARVAR_NOT_REQUIRED);
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

            // The pruning pages define sections of the page landscape that this block applies to.
            if (!isset($data['prune_pids'])) $vars['prune_pids'] = $this->prune_pids;
            else $vars['prune_pids'] = $data['prune_pids'];

            xarVarFetch('new_prune_pid', 'int:0', $new_prune_pid, 0, XARVAR_NOT_REQUIRED) && !empty($new_prune_pid);
            $vars['prune_pids'][] = $new_prune_pid;

            xarVarFetch('remove_prune_pid', 'list:int:1', $remove_prune_pid, array(), XARVAR_NOT_REQUIRED);
            // Easier to check with the keys and values flipped.
            $vars['prune_pids'] = array_flip($vars['prune_pids']);
            foreach($remove_prune_pid as $remove) {
                if (isset($vars['prune_pids'][$remove])) {
                    unset($vars['prune_pids'][$remove]);
                }
            }
            // Flip keys and values back.
            $vars['prune_pids'] = array_flip($vars['prune_pids']);
            // Reorder the keys.
            $vars['prune_pids'] = array_values($vars['prune_pids']);

            // The maximum number of levels that are displayed.
            // This value does not affect the tree data, but is passed to the menu rendering
            // templates to make its own decision on how to truncate the menu.
            xarVarFetch('max_level', 'int:0:999', $vars['max_level'], 0, XARVAR_NOT_REQUIRED);

            // The start level.
            // Hide the menu if the current page is below this level.
            xarVarFetch('start_level', 'int:0:999', $vars['start_level'], 0, XARVAR_NOT_REQUIRED);
            
            $this->setContent($vars);
            return true;

        }
    }

?>
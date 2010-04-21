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

    sys::import('modules.xarpages.xarblocks.xarpagesmenu');

    class Xarpages_MenuBlockAdmin extends Xarpages_MenuBlock implements iBlock
    {
/**
 * Modify Function to the Blocks Admin
 * @author Jason Judge
 * @param $blockinfo array containing title,content
 */

        public function modify(Array $data=array())
        {
            $data = parent::modify($data);

            // Defaults
            if (!isset($data['multi_homed'])) {$data['multi_homed'] = $this->multi_homed;}
            if (!isset($data['current_source'])) {$data['current_source'] = $this->current_source;}
            if (!isset($data['default_pid'])) {$data['default_pid'] = $this->default_pid;}
            if (!isset($data['max_level'])) {$data['max_level'] = $this->max_level;}
            if (!isset($data['start_level'])) {$data['start_level'] = $this->start_level;}
            if (!isset($data['root_pids'])) {$data['root_pids'] = $this->root_pids;}
            if (!isset($data['prune_pids'])) {$data['prune_pids'] = $this->prune_pids;}

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
        public function update(Array $data=array())
        {
            $data = parent::update($data);
    
            if (xarVarFetch('multi_homed', 'checkbox', $multi_homed, 1, XARVAR_NOT_REQUIRED)) {
                $data['multi_homed'] = $multi_homed;
            }

            // AUTO: the block picks up the page from cache Blocks.xarpages/current_pid.
            // DEFAULT: the block always uses the default page.
            // AUTODEFAULT: same as AUTO, but use the default page rather than NULL if outside and root page
            if (xarVarFetch('current_source', 'pre:upper:passthru:enum:AUTO:DEFAULT:AUTODEFAULT', $current_source, 'AUTO', XARVAR_NOT_REQUIRED)) {
                $data['current_source'] = $current_source;
            }

            // The default page if none found by any other method.
            if (xarVarFetch('default_pid', 'int:0', $default_pid, 0, XARVAR_NOT_REQUIRED)) {
                $data['default_pid'] = $default_pid;
            }

            // The root pages define sections of the page landscape that this block applies to.
            if (!isset($data['root_pids'])) $data['root_pids'] = $this->root_pids;

            if (xarVarFetch('new_root_pid', 'int:0', $new_root_pid, 0, XARVAR_NOT_REQUIRED) && !empty($new_root_pid)) {
                $data['root_pids'][] = $new_root_pid;
            }
            if (xarVarFetch('remove_root_pid', 'list:int:1', $remove_root_pid, array(), XARVAR_NOT_REQUIRED) && !empty($remove_root_pid)) {
                // Easier to check with the keys and values flipped.
                $data['root_pids'] = array_flip($data['root_pids']);
                foreach($remove_root_pid as $remove) {
                    if (isset($data['root_pids'][$remove])) {
                        unset($data['root_pids'][$remove]);
                    }
                }
                // Flip keys and values back.
                $data['root_pids'] = array_flip($data['root_pids']);
                // Reorder the keys.
                $data['root_pids'] = array_values($data['root_pids']);
            }

            // The pruning pages define sections of the page landscape that this block applies to.
            if (!isset($data['prune_pids'])) $data['prune_pids'] = $this->prune_pids;

            if (xarVarFetch('new_prune_pid', 'int:0', $new_prune_pid, 0, XARVAR_NOT_REQUIRED) && !empty($new_prune_pid)) {
                $data['prune_pids'][] = $new_prune_pid;
            }
            if (xarVarFetch('remove_prune_pid', 'list:int:1', $remove_prune_pid, array(), XARVAR_NOT_REQUIRED) && !empty($remove_prune_pid)) {
                // Easier to check with the keys and values flipped.
                $data['prune_pids'] = array_flip($data['prune_pids']);
                foreach($remove_prune_pid as $remove) {
                    if (isset($data['prune_pids'][$remove])) {
                        unset($data['prune_pids'][$remove]);
                    }
                }
                // Flip keys and values back.
                $data['prune_pids'] = array_flip($data['prune_pids']);
                // Reorder the keys.
                $data['prune_pids'] = array_values($data['prune_pids']);
            }

            // The maximum number of levels that are displayed.
            // This value does not affect the tree data, but is passed to the menu rendering
            // templates to make its own decision on how to truncate the menu.
            if (xarVarFetch('max_level', 'int:0:999', $max_lavel, 0, XARVAR_NOT_REQUIRED)) {
                $data['max_level'] = $max_lavel;
            }

            // The start level.
            // Hide the menu if the current page is below this level.
            if (xarVarFetch('start_level', 'int:0:999', $start_lavel, 0, XARVAR_NOT_REQUIRED)) {
                $data['start_level'] = $start_lavel;
            }

            return $data;
        }
    }

?>

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

    sys::import('modules.publications.xarblocks.menu');

    class Publications_MenuBlockAdmin extends Publications_MenuBlock implements iBlock
    {
/**
 * Modify Function to the Blocks Admin
 * @author Jason Judge
 * @param $blockinfo array containing title,content
 */

        public function modify(Array $data=array())
        {
            $data = $this->getContent();

            // Get a list of all pages for the drop-downs.
            // Get the tree of all pages, without the DD for speed.
            $data['all_pages'] = xarMod::apiFunc(
                'publications', 'user', 'getpagestree',
                array('dd_flag' => false, 'key' => 'pid')
            );

            // Implode the names for each page into a path for display.
            // TODO: move this into getpagestree
            foreach ($data['all_pages']['pages'] as $key => $page) {
                $data['all_pages']['pages'][$key]['slash_separated'] =  '/' . implode('/', $page['namepath']);
            }

            // Get the descriptions together for the current root ids.
            // TODO: we could prune the 'add root page' list so it only includes
            // the pages which are not yet under one of the selected root pages.
            // That would just be an extra little usability touch.
            $data['root_ids'] = array_flip($data['root_ids']);
            foreach($data['root_ids'] as $key => $value) {
                if (isset($data['all_pages']['pages'][$key])) {
                    $data['root_ids'][$key] = $data['all_pages']['pages'][$key]['slash_separated'];
                } else {
                    $data['root_ids'][$key] = xarML('Unknown');
                }
            }

            $data['prune_ids'] = array_flip($data['prune_ids']);
            foreach($data['prune_ids'] as $key => $value) {
                if (isset($data['all_pages']['pages'][$key])) {
                    $data['prune_ids'][$key] = $data['all_pages']['pages'][$key]['slash_separated'];
                } else {
                    $data['prune_ids'][$key] = xarML('Unknown');
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
            xarVar::fetch('multi_homed', 'int', $args['multi_homed'], 0, XARVAR_NOT_REQUIRED);

            // AUTO: the block picks up the page from cache Blocks.publications/current_id.
            // DEFAULT: the block always uses the default page.
            // AUTODEFAULT: same as AUTO, but use the default page rather than NULL if outside and root page
            xarVar::fetch('current_source', 'pre:upper:passthru:enum:AUTO:DEFAULT:AUTODEFAULT', $args['current_source'], 'AUTO', XARVAR_NOT_REQUIRED);

            // The default page if none found by any other method.
            xarVar::fetch('default_id', 'int:0', $args['default_id'], 0, XARVAR_NOT_REQUIRED);

            // The root pages define sections of the page landscape that this block applies to.
            if (!isset($data['root_ids'])) $args['root_ids'] = $this->root_ids;
            else $args['root_ids'] = $data['root_ids'];

            xarVar::fetch('new_root_id', 'int:0', $new_root_id, 0, XARVAR_NOT_REQUIRED);
            if (!empty($new_root_id)) $args['root_ids'][] = $new_root_id;

            xarVar::fetch('remove_root_id', 'list:int:1', $remove_root_id, array(), XARVAR_NOT_REQUIRED);
            // Easier to check with the keys and values flipped.
            $args['root_ids'] = array_flip($args['root_ids']);
            foreach($remove_root_id as $remove) {
                if (isset($args['root_ids'][$remove])) {
                    unset($args['root_ids'][$remove]);
                }
            }
            // Flip keys and values back.
            $args['root_ids'] = array_flip($args['root_ids']);
            // Reorder the keys.
            $args['root_ids'] = array_values($args['root_ids']);

            // The pruning pages define sections of the page landscape that this block applies to.
            if (!isset($data['prune_ids'])) $args['prune_ids'] = $this->prune_ids;
            else $args['prune_ids'] = $data['prune_ids'];

            xarVar::fetch('new_prune_id', 'int:0', $new_prune_id, 0, XARVAR_NOT_REQUIRED);
            if (!empty($new_prune_id)) $args['prune_ids'][] = $new_prune_id;

            xarVar::fetch('remove_prune_id', 'list:int:1', $remove_prune_id, array(), XARVAR_NOT_REQUIRED);
            // Easier to check with the keys and values flipped.
            $args['prune_ids'] = array_flip($args['prune_ids']);
            foreach($remove_prune_id as $remove) {
                if (isset($args['prune_ids'][$remove])) {
                    unset($args['prune_ids'][$remove]);
                }
            }
            // Flip keys and values back.
            $args['prune_ids'] = array_flip($args['prune_ids']);
            // Reorder the keys.
            $args['prune_ids'] = array_values($args['prune_ids']);

            // The maximum number of levels that are displayed.
            // This value does not affect the tree data, but is passed to the menu rendering
            // templates to make its own decision on how to truncate the menu.
            xarVar::fetch('max_level', 'int:0:999', $args['max_level'], 0, XARVAR_NOT_REQUIRED);

            // The start level.
            // Hide the menu if the current page is below this level.
            xarVar::fetch('start_level', 'int:0:999', $args['start_level'], 0, XARVAR_NOT_REQUIRED);

            $this->setContent($args);
            return true;
        }
    }

?>
<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Jason Judge
 */

/**
 * Displays a crumb-trail block
 * Shows the visitor's current position in the page hierarchy
*/
sys::import('modules.publications.xarblocks.crumb');

class Publications_CrumbBlockAdmin extends Publications_CrumbBlock implements iBlock
{
    /**
     * Modify Function to the Blocks Admin
     * @param none
     * @return array template data
     */
    public function modify()
    {
        $data = $this->getContent();

        // Defaults
        if (!isset($data['include_root'])) {$data['include_root'] = false;}
        if (!isset($data['root_ids'])) {$data['root_ids'] = array();}

        // Get a list of all pages for the drop-downs.
        // Get the tree of all pages, without the DD for speed.
        $data['all_pages'] = xarMod::apiFunc(
            'publications', 'user', 'getpagestree',
            array('dd_flag' => false, 'key' => 'id')
        );

        // Implode the names for each page into a path for display.
        // TODO: move this into getpagestree
        $data['options'] = array();
        foreach ($data['all_pages']['pages'] as $key => $page) {
            $data['options'][$page['id']] = array('id' => $page['id'], 'name' =>  '/' . implode('/', $page['namepath']));
        }

        // Get the descriptions together for the current root pids.
        // TODO: we could prune the 'add root page' list so it only includes
        // the pages which are not yet under one of the selected root pages.
        // That would just be an extra little usability touch.
        $data['root_ids'] = array_flip($data['root_ids']);
        foreach($data['root_ids'] as $key => $value) {
            if (isset($data['options'][$key])) {
                $data['root_ids'][$key] = $data['options'][$key]['name'];
            } else {
                $data['root_ids'][$key] = xarML('Unknown');
            }
        }

        return $data;
    }
/**
 * Updates the Block config from the Blocks Admin
 * @param none
 * @return bool true on success
 */
    public function update()
    {
        $vars = $this->getContent();

        if (xarVar::fetch('include_root', 'checkbox', $include_root, 0, XARVAR_NOT_REQUIRED)) {
            $vars['include_root'] = $include_root;
        }

        // The root pages define sections of the page landscape that this block applies to.
        if (!isset($vars['root_ids'])) {
            $vars['root_ids'] = array();
        }
        if (xarVar::fetch('new_root_pid', 'int:0', $new_root_pid, 0, XARVAR_NOT_REQUIRED) && !empty($new_root_pid)) {
            $vars['root_ids'][] = $new_root_pid;
        }
        if (xarVar::fetch('remove_root_pid', 'list:int:1', $remove_root_pid, array(), XARVAR_NOT_REQUIRED) && !empty($remove_root_pid)) {
            // Easier to check with the keys and values flipped.
            $vars['root_ids'] = array_flip($vars['root_ids']);
            foreach($remove_root_pid as $remove) {
                if (isset($vars['root_ids'][$remove])) {
                    unset($vars['root_ids'][$remove]);
                }
            }
            // Flip keys and values back.
            $vars['root_ids'] = array_flip($vars['root_ids']);
            // Reorder the keys.
            $vars['root_ids'] = array_values($vars['root_ids']);
        }
        $this->setContent($vars);
        return true;

    }
}
?>

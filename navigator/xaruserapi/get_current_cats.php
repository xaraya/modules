<?php
/**
 * CHSF Content Navigation Block
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2004 by the Schwab Foundation
 * @link http://wwwk.schwabfoundation.org
 *
 * @subpackage navigator module
 * @author Carl Corliss <Rabbitt : rabbitt@xaraya.com>
*/

/**
 * Parse category ids
 *
 * @author Carl Corliss
 * @param $args an array of arguments
 * @param $args['catid'] cids of an article
 * @returns array, or false on failure
 * @raise BAD_DATA
 */
function navigator_userapi_get_current_cats( /* VOID */ )
{

    // set up the list to be cached
    static $list = array();

    // Return the cached list if available
    if (!empty($list)) {
        return $list;
    } else {

        $matrix = xarModGetVar('navigator', 'style.matrix') ? TRUE : FALSE;
        $catList = xarVarGetCached('Blocks.articles', 'cids');

        if (!is_array($catList)) {
            return array();
        }

        // Grab the default Parent Category List
        $prilist  = @unserialize(xarModGetVar('navigator', 'categories.list.primary'));
        $seclist  = @unserialize(xarModGetVar('navigator', 'categories.list.secondary'));


        xarModAPIFunc('navigator', 'user', 'nested_tree_flatten', &$prilist);
        xarModAPIFunc('navigator', 'user', 'nested_tree_flatten', &$seclist);

        if (!isset($prilist) || empty($prilist)) {
            // Make sure we don't bail out due to it being unset
            $primary_list = array();
        } else {
            foreach ($prilist as $item) {
                $primary_list[$item['cid']] = $item;
            }
        }

        if (!isset($seclist) || empty($seclist)) {
            // Make sure we don't bail out due to it being unset
            $secondary_list = array();
        } else {
            foreach ($seclist as $item) {
                $secondary_list[$item['cid']] = $item;
            }
        }

        if (count($catList) > 1) {
            if ($catList[0] == $catList[1]) {
                // if there's a duplicate, delete it
                unset($catList[1]);
            }
        }

        // Now we grab the name for the first id and, if there are any other ids
        // we grab their primaryId/primaryName, and catId/catName
        foreach ($catList as $key => $catId) {
            if (isset($primary_list[$catId])) {
                $list['primary']   = array('id' => $catId, 'name' => $primary_list[$catId]['name']);
            } elseif (isset($secondary_list[$catId])) {
                $list['secondary'] = array('id' => $catId, 'name' => $secondary_list[$catId]['name']);
            }
        }

        if (count($list) == 1 && $matrix) {
            if (isset($list['primary'])) {
                $secondary = xarModGetVar('navigator', 'categories.secondary.default');
                $list['secondary'] = array('id' => $secondary, 'name' => $secondary_list[$secondary]['name']);
            } else {
                $list = array();
            }
        }
        return $list;
    }

}

?>

<?php
/**
 * Comments Module
 *
 * @package modules
 * @subpackage comments
 * @category Third Party Xaraya Module
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 *  Recurse through an array and reassign the celko based
 *  left and right values for each node
 *
 *  @author Carl P. Corliss
 *  @access private
 *  @param  array   $data  The array containing all the data nodes to adjust
 *  @returns array  the modified array is passed back, or zero if it is empty
 */

function comments_adminapi_celko_assign_slots( $data )
{

    static $total = 0;
    static $depth = 0;

    if (!is_array($data)) {
        return 0;
    }

    foreach ($data as $node_id => $node_data) {
        $node_data['depth'] = $depth++;
        $node_data['left_id']  = $total++;
    if (isset($node_data['children'])) {
            $node_data['children'] = xarMod::apiFunc('comments',
                                                   'admin',
                                                   'celko_assign_slots',
                                                    $node_data['children']);
        } else {
            $node_data['children'] = FALSE;
        }
        $node_data['right_id'] = $total++;
        $depth--;
        $tree[$node_id] = $node_data;
    }

    return $tree;
}

?>

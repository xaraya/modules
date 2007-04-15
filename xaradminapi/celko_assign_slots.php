<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Comments Module
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
        $node_data['cleft']  = $total++;
    if (isset($node_data['children'])) {
            $node_data['children'] = xarModAPIFunc('comments',
                                                   'admin',
                                                   'celko_assign_slots',
                                                    $node_data['children']);
        } else {
            $node_data['children'] = FALSE;
        }
        $node_data['cright'] = $total++;
        $depth--;
        $tree[$node_id] = $node_data;
    }

    return $tree;
}

?>

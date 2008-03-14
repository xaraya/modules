<?php

/**
 * File: $Id$
 *
 * Render functions for hierarchies.
 *
 * @package modules
 * @copyright (C) 2002-2004 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage xarpages
 * @author Carl P. Corlis/Jason Judge
 */

/**
 * Inclusion of common defines
 */

// Maximum allowable branch depth
define('_XARPAGES_MAX_DEPTH', 20);

// These defines are threaded view specific and should be here
// Used for creation of the visual (threaded) tree
define('_XARPAGES_NO_CONNECTOR', 'N');      // '' (zero-width)
define('_XARPAGES_O_CONNECTOR', 'O');       // o- (root with no children)
define('_XARPAGES_P_CONNECTOR', 'P');       // P  (root with children)
define('_XARPAGES_DASH_CONNECTOR', '-');    // --
define('_XARPAGES_T_CONNECTOR', '+');       // +- (non-last child in a group)
define('_XARPAGES_L_CONNECTOR', 'L');       // |_
define('_XARPAGES_I_CONNECTOR', '|');       // |
define('_XARPAGES_BLANK_CONNECTOR', 'B');   // '  ' (spacer)

/**
 * Takes a an array of related (parent -> child) values and assigns a depth to
 * each one -- requires that each node in the array has the 'children' field
 * telling how many children it [the current node] has
 * List passed as argument MUST be an ordered list - in the order of
 * Parent1 -> child2-> child3 -> child4 -> subchild5 -> sub-subchild6-> subchild7-> child8-> child9-> subchild10 -> Parent11 ->....
 * for example, the below list is an -ordered list- in thread order (ie., parent to child relation ships):
 * <pre>
 *
 *   ID | VISUAL       |   DEPTH
 *   ===+==============+=========
 *    1 | o            |   0
 *      | |            |
 *    2 | +--          |   1
 *      | |            |
 *    3 | +--          |   1
 *      | |            |
 *    4 | +--o         |   1
 *      | |  |         |
 *    5 | +  +--o      |   2
 *      | |  |  |      |
 *    6 | +  +  +--    |   3
 *      | |  |         |
 *    7 | +  +--       |   2
 *      | |            |
 *    8 | +--          |   1
 *      | |            |
 *    9 | +--o         |   1
 *      |    |         |
 *   10 |    +--       |   2
 *      |              |
 *   11 | o            |   0
 *      | |            |
 *   12 | +--o         |   1
 *      |    |         |
 *   13 |    +--       |   2
 *
 * </pre>
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access public
 * @param array &$comments_list  A reference (pointer) to an array or related items in parent -> child order (see above)
 * @returns bool true on success, false otherwise
 *
 */


/**
 * Maps out the visual structure of a tree based on each
 * node's 'depth' and 'children' fields
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access public
 * @param array      $items   List of related comments
 * @returns array an array of comments with an extra field ('map') for each comment
 *               that's contains the visual representation for that particular node
 * @todo remove the need for both this function and 'depthbuoy' to maintain separate matrix arrays
 */

function xarpages_treeapi_array_maptree($items)
{
    // If $items isn't an array or it is empty,
    // raise an exception and return an empty array.
    if (!is_array($items) || count($items) == 0) {
        // TODO: Raise Exception
        return array();
    }

    $current_depth  = 0;         // depth of the current comment in the array
    $next_depth     = 0;         // depth of the next comment in the array (closer to beginning of array)
    $prev_depth     = 0;         // depth of the previous comment in the array (closer to end of array)
    $matrix         = array();   // initialize the matrix to a null array
    $depth_flags    = array();

    $total = count($items);
    $listsize = $total - 1;

    $depth_flags = array_pad(array(0 => 0), _XARPAGES_MAX_DEPTH, false);

    // Create the matrix starting from the end and working our way towards
    // the beginning.
    // FIXME: the items array is not necessarily indexed by a sequential number.
    for ($counter = $listsize; $counter >= 0; $counter -= 1) {
        // Unmapped matrix for current page.
        $matrix = array_pad(array(0 => 0), _XARPAGES_MAX_DEPTH, _XARPAGES_NO_CONNECTOR);

        // Make sure to $depth = $depth modulus _XARPAGES_MAX_DEPTH  - because we are only ever showing
        // limited levels of depth.
        $current_depth  = @$items[$counter]['depth'] % _XARPAGES_MAX_DEPTH;
        $next_depth     = (($counter -1) < 0 ? -1 : @$items[$counter-1]['depth'] % _XARPAGES_MAX_DEPTH);
        $prev_depth     = (($counter +1) > $listsize ? -1 : @$items[$counter+1]['depth'] % _XARPAGES_MAX_DEPTH);

        // first start by placing the depth point in the matrix
        // if the current comment has children place a P connetor
        if (!empty($items[$counter]['child_keys'])) {
            $matrix[$current_depth] = _XARPAGES_P_CONNECTOR;
        } else {
            // if the current comment doesn't have children
            // and it is at depth ZERO it is an O connector
            // otherwise use a dash connector
            if (!$current_depth) {
                $matrix[$current_depth] = _XARPAGES_O_CONNECTOR;
            } else {
                $matrix[$current_depth] = _XARPAGES_DASH_CONNECTOR;
            }
        }

        // if the current depth is zero then all that it requires is an O or P connector
        // soooo if the current depth is -not- zero then we have other connectors so
        // below we figure out what the other connectors are...
        if (0 != $current_depth) {
            if ($current_depth != $prev_depth) {
                $matrix[$current_depth - 1] = _XARPAGES_L_CONNECTOR;
            }

            // In order to have a T connector the current depth must
            // be less than or equal to the previous depth.
            if ($current_depth <= $prev_depth) {
                // If there is a DepthBuoy set for (current depth -1) then
                // we need a T connector.
                if ($current_depth == 0 || $depth_flags[$current_depth-1]) {
                    $depth_flags[$current_depth-1] = false;
                    $matrix[$current_depth - 1] = _XARPAGES_T_CONNECTOR;
                }

                if ($current_depth == $prev_depth) {
                    $matrix[($current_depth - 1)] = _XARPAGES_T_CONNECTOR;
                }

            }

            // Once we've got the T and L connectors done, we need to go through
            // the matrix working our way from the indice equal to the current item
            // depth towards the begginning of the array - checking for I connectors
            // and Blank connectors.
            for ($node = $current_depth; $node >= 0; $node -= 1) {
                // Be sure not to overwrite another node in the matrix
                if ($matrix[$node] == _XARPAGES_NO_CONNECTOR) {
                    // If a depth buoy was set for this depth, add I connector.
                    if ($depth_flags[$node]) {
                        $matrix[$node] = _XARPAGES_I_CONNECTOR;
                    } else {
                        // Otherwise add a blank connector (a spacer).
                        $matrix[$node] = _XARPAGES_BLANK_CONNECTOR;
                    }
                }
            }
        }

        // Set depth buoy if the next depth is greater then the current,
        // this way we can remember where to set an I connector.
        if (($next_depth > $current_depth) && ($current_depth != 0)) {
            // JJ
            $depth_flags[$current_depth - 1] = true;
        }

        // TODO: the padded-out matrix is wasteful (many calls to the image translation
        // function done when the number of pages is large). Refactor so no padding is
        // required.
        $items[$counter]['xar_map'] = implode('', array_map("xarpages_treeapi_array_image_substitution", $matrix));
    }

    return $items;
}


/**
 * Used internally by xarpages_xartreeapi_array_maptree(). Takes the nodes in a matrix created for
 * a particular comment and translates them into HTML images.
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access private
 * @param integer    $matrix    the numerical representation of this segment of the visual map
 * @returns string    a visual (html'ified) map of the matrix
 * @todo Wouldn't it be nice to be able to join these images together into a single image for each page and cache them?
 */

function xarpages_treeapi_array_image_substitution($node)
{
    static $image_list = NULL;

    if (!isset($image_list)) {
        $style = 'class="xar-xarpages-tree"';

        $image_list[_XARPAGES_O_CONNECTOR] =
            '<img '.$style.' src="' . xarTplGetImage('n_nosub.gif', 'xarpages') . '" alt="0" />';
        $image_list[_XARPAGES_P_CONNECTOR] =
            '<img '.$style.' src="' . xarTplGetImage('n_sub.gif', 'xarpages') . '" alt="P" />';
        $image_list[_XARPAGES_T_CONNECTOR] =
            '<img '.$style.' src="' . xarTplGetImage('n_sub_branch_t.gif', 'xarpages') . '" alt="t" />';
        $image_list[_XARPAGES_L_CONNECTOR] =
            '<img '.$style.' src="' . xarTplGetImage('n_sub_branch_l.gif', 'xarpages') . '" alt="L" />';
        $image_list[_XARPAGES_I_CONNECTOR] =
            '<img '.$style.' src="' . xarTplGetImage('n_sub_line.gif', 'xarpages') . '" alt="|" />';
        $image_list[_XARPAGES_BLANK_CONNECTOR] =
            '<img '.$style.' src="' . xarTplGetImage('n_spacer.gif', 'xarpages') . '" alt="&#160;" />';
        $image_list[_XARPAGES_DASH_CONNECTOR] =
            '<img '.$style.' src="' . xarTplGetImage('n_sub_end.gif', 'xarpages') . '" alt="_" />';
        $image_list[_XARPAGES_NO_CONNECTOR] = '';
    }

    if (isset($image_list[$node])) {
        return $image_list[$node];
    } else {
        return '';
    }
}

?>
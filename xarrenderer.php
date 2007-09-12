<?php
/**
 * Categories module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Categories Module
 * @link http://xaraya.com/index.php/release/147.html
 * @author Categories module development team
 */
/**
 * Inclusion of common defines
 * Render functions for the categories module
 *
 * @author Carl P. Corlis
 */
include_once('modules/categories/xarincludes/common/defines.php');


// These defines are threaded view specific and should be here
// Used for creation of the visual (threaded) tree
define('_CAT_NO_CONNECTOR',0);
define('_CAT_O_CONNECTOR', 1);
define('_CAT_P_CONNECTOR', 2);
define('_CAT_DASH_CONNECTOR',3);
define('_CAT_T_CONNECTOR', 4);
define('_CAT_L_CONNECTOR', 5);
define('_CAT_I_CONNECTOR',6);
define('_CAT_BLANK_CONNECTOR',7);
define('_CAT_CUTOFF_CONNECTOR',8);


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
function categories_renderes_array_markdepths_bychildren(&$comments_list)
{

    // check to make sure we got passed an array,
    // return false if we got no array or it has no items in it
    if (!is_array($comments_list) || !count($comments_list))
        return false;

    // figure out how man total nodes are in this array,
    $total_nodes = count($comments_list);

    // check to see if this array has the depth field in it already,
    // if not, it's the first time this array has been parsed through
    // this function so initialize each node to have a depth of zero:
    if (!isset($comments_list[0]['depth'])) {
        for ( $node = 0; $node < $total_nodes ; $node++ ) {
            $comments_list[$node]['depth'] = 0;
        }
    }

    for ( $node = 0; $node < $total_nodes ; $node++) {

        // if the current node has zero (or less) children,
        // skip to the next one
        if ($comments_list[$node]['children'] <= 0) {
            continue;
        } else {  // otherwise, the node has children so figure out it's last child's index number
            $last_child = $node + $comments_list[$node]['children'];
        }

        // now we increment starting at the node's first child up
        // to it's last one adding one to each of it's kids
        for ($index = $node + 1; ($index <= $last_child) && ($index < $total_nodes) ; $index++) {
            $comments_list[$index]['depth'] += 1;
        }
    }

    return true;
}

/**
 * Takes a an array of related (parent -> child) values and assigns a depth to
 * each one -- requires that each node in the array has the 'pid' (parent id) field
 * List passed as argument MUST be an ordered list - in the order of
 * Parent1 -> child2-> child3 -> child4 -> subchild5 -> sub-subchild6-> subchild7-> child8-> child9-> subchild10 -> Parent11 ->....
 * This function is exactly like comments_display_array_markdepths but tailored for
 * use with parent id's instead.
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access public
 * @param array   &$comments_list    an array of related (array) items - each item -must- contain a parent id field
 * @returns bool True on success, False otherwise
 */
function categories_renderer_array_markdepths_bypid(&$comments_list)
{

    if (empty($comments_list) || !count($comments_list)) {
        xarErrorSet(XAR_SYSTEM_EXCEPTION,BAD_PARAM,'Category list is empty');
        return;
    }

    // start the initial depth off at zero
    $depth = 0;

    $parents = array();
    $new_list = array();
    $prep_list = array();

    // Initialize parents array and make the first key in it equal
    // to the first node in the array's parentid
    $parents["PID_{$comments_list[0]['xar_pid']}"] = $depth;

    // setup the keys for each comment so that we can
    // easily reference them further down
    foreach($comments_list as $node) {
        $new_list[$node['xar_cid']] = $node;
    }
    $comments_list = $new_list;

    // re-initialize the new_list array
    $new_list = array();

    // foreach node in the array, check to see if we
    // have it's parent id marked in memory and, if so
    // set the current nodes depth equal to that of
    // the marked parent id. If not, then we need to
    // add the depth for the current parent id to the
    // parents list for future use :)
    foreach ($comments_list as $key => $node) {
        // if the current node's parent isn't yet
        // defined, then add it to the list of parents
        // and give it a depth equal to it's parent's depth + 1
        if (!array_key_exists("PID_".$node['xar_pid'],$parents)) {
            if (!array_key_exists($node['xar_pid'], $comments_list)) {
                $comments_list[$node['xar_pid']]['xar_pid'] = 0;
                $comments_list[$node['xar_pid']]['xar_cid'] = 0;
                $comments_list[$node['xar_pid']]['remove'] = 'remove';
                $parents["PID_".$node['xar_pid']] = -1;
            }
            $ppidkey = "PID_".$comments_list[$node['xar_pid']]['xar_pid'];
        // CHECKME: when we start with a category 2+ levels deep, $parents['PID_0'] is undefined here
            if (!isset($parents[$ppidkey])) {
                $parents[$ppidkey] = -1;
            }
            $parents["PID_".$node['xar_pid']] = $parents[$ppidkey] + 1;
        }

        // if the current nodes parent already has
        // has a defined depth and that depth is
        // zero, then reset the $depth counter to zero
        if (0 == $parents['PID_'.$node['xar_pid']]) {
            $depth = 0;
        }

        $prep_list[$key] = $node;
         if (isset($comments_list[$key]['indentation'])) {
            $prep_list[$key]['depth'] =  $comments_list[$key]['indentation'] - 1;
         } else {
            $prep_list[$key]['depth'] = $parents["PID_".$node['xar_pid']];
    //        $prep_list[$key]['depth'] =  0;
         }

         //Allow parents to appear as so even if their children is only in the next page.
         if (isset($comments_list[$key]['right']) && ($comments_list[$key]['right'] - $comments_list[$key]['left']) > 1) {
            $prep_list[$key]['children'] = 1;
         } else {
            $prep_list[$key]['children'] = 0;
         }


    }
    // now we go through and find all the nodes that were marked
    // as parent nodes and add the 'haschildren' field to them
    // setting it to true -- otherwise, if the node wasn't a
    // parent ID we set it's 'haschildren' equal to false
    /*

    foreach ($prep_list as $node) {
        if (isset($parents["PID_".$node['xar_cid']])) {
            $node['children'] = 1;
            unset($parents["PID_".$node['xar_cid']]);
        } else {
            $node['children'] = 0;
        }
        $new_list[] = $node;
    }
    */

    $comments_list = '';

    // remove any items that aren't really a part of the array
    // and are just excess baggage from previous code
    foreach ($prep_list as $node) {
        if (!array_key_exists('remove',$node)) {
            $comments_list[] = $node;
        }
    }
    // free up the variables that we
    // created for this function...
    unset($new_list);
    unset($parents);
    unset($prep_list);

    return true;


}


/**
 * Remove any comments from the list with a depth greater than
 * the cutoff point. If the depth of any particular node is equal
 * to (cutoff + 1), then just the cid and the depth for that particular
 * node are included in the array. Reason: it allows us to show that
 * there are more comments in that direction. This is used by
 * comments_userapi_get() to limit the comments pulled by depth.
 *
 * @access private
 * @author Carl P. Corliss (aka rabbitt)
 * @param array      $args['array_list']    list of comments to check
 * @param integer    $args['cutoff']        depth cutoff point
 * @returns void if no array is passed or the array has no nodes return void
 */
function categories_renderer_array_prune_excessdepth($args)
{

    extract($args);
    if (!is_array($array_list) || !count($array_list)) {
        // TODO: raise exception
        return;
    }

    $new_list = array();
    foreach ($array_list as $node) {
        if ($cutoff == $node['depth']) {
            $childcount = comments_userapi_get_childcount($node['xar_cid']);
            if ($childcount) {

                $new_subject = $node['xar_subject'] . "  ( $childcount "
                             . (($childcount > 1) ? xarML("children") : xarML("child")) .")";

                $new_list[] = array('xar_cid'   => $node['xar_cid'],
                                    'xar_branchout' => true,
                                    'depth'     => $node['depth'],
                                    'children'  => (int) '-1',
                                    'xar_subject'   => $new_subject,
                                    'xar_author'=> $node['xar_author'],
                                    'xar_date' => $node['xar_date']);
            } else {
                // if the comment doesn't have any children, then
                // display it normally...
                $node['xar_branchout'] = 0;
                $new_list[] = $node;
            }
        } elseif ($node['depth'] > $cutoff) {
            continue;
        } else {
            $node['xar_branchout'] = 0;
            $new_list[] = $node;
        }
    }

    $array_list = $new_list;
    unset($new_list);

    return $array_list;
}

/**
 * Used internally by categories_renderer_array_maptree() to keep track
 * of depths while mapping out the visual tree structure
 *
 * @access private
 * @author Carl P. Corliss (aka rabbitt)
 * @param string     $action    get or set
 * @param integer    $depth     the depth to set or get
 * @param bool       $value     true if the depth is set or false if unset
 * @returns bool true if the specified depth is set, false otherwise
 */

function categories_renderer_array_depthbuoy($action, $depth, $value=true)
{

    static $matrix = array();

    if (empty($matrix)) {
        $matrix = array_pad(array(0=>0), _CAT_MAX_DEPTH, _CAT_NO_CONNECTOR);
    }

    if (strtolower($action) == 'set') {
        $matrix[($depth)] = (bool) $value;
    }

    if ($depth < 0) {
        return 0;
    } else {
        return $matrix[($depth)];
    }
}

/**
 * Maps out the visual structure of a tree based on each
 * node's 'depth' and 'children' fields
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access public
 * @param array      $CommentList   List of related comments
 * @returns array an array of comments with an extra field ('map') for each comment
 *               that's contains the visual representation for that particular node
 */

function categories_renderer_array_maptree($CommentList)
{

    // if $CommentList isn't an array or it is empty,
    // raise an exception and return an empty array
    if (!is_array($CommentList) || count($CommentList) == 0) {
        // TODO: Raise Exception
        return array();
    }

    // if comments in the list don't have depth then we can't generate
    // the visual image -- so, in that case, see if the comments
    // have a children field. If they do, setup the depths for each
    // comment based on that -- if not, check for a pid field and
    // then set up the depth fields for each if that is present,
    // otherwise -- raise an exception.  Also, sort them after
    // assigning depths.
/*
    if (isset($CommentList[0]['depth']) < 0) {
        if (!isset($CommentList[0]['children'])) {
            if (!isset($CommentList[0]['pid'])) {
                // TODO: Raise Exception
            } else {
                categories_renderer_array_markdepths_pid($CommentList);
            }
        } else {
            categories_renderer_array_markdepths_bychildren($CommentList);
        }
        // if we don't have depths then we can (safely) assume we don't
        // have a sorted list either -- so sort.

        categories_renderer_array_sort($CommentList, _CAT_SORTBY_THREAD, _CAT_SORT_ASC);
    }
*/
    $current_depth  = 0;         // depth of the current comment in the array
    $next_depth     = 0;         // depth of the next comment in the array (closer to beginning of array)
    $prev_depth     = 0;         // depth of the previous comment in the array (closer to end of array)
    $matrix         = array();   // initialize the matrix to a null array

    $listsize = (count($CommentList) - 1);
    $total = count($CommentList);

    // create the matrix starting from the end and working our way towards
    // the beginning.
    for ($counter = $listsize; $counter >= 0; ($counter = $counter - 1)) {

        // unmapped matrix for current comment
        $matrix = array_pad(array(0=>0), _CAT_MAX_DEPTH, _CAT_NO_CONNECTOR);

        // make sure to $depth = $depth modulus _CAT_MAX_DEPTH  - because we are only ever showing
        // ten levels of depth -- anything more than that and the display doesn't look good
        $current_depth  = @$CommentList[$counter]['depth'] % _CAT_MAX_DEPTH;
        $next_depth     = (($counter -1) < 0 ? -1 : @$CommentList[$counter-1]['depth'] % _CAT_MAX_DEPTH);
        $prev_depth     = (($counter +1) > $listsize ? -1 : @$CommentList[$counter+1]['depth'] % _CAT_MAX_DEPTH);

        // first start by placing the depth point in the matrix
        // if the current comment has children place a P connetor
        if ($CommentList[$counter]['children'] === true || $CommentList[$counter]['children'] > 0) {
            $matrix[$current_depth] = _CAT_P_CONNECTOR;
        } elseif ($CommentList[$counter]['children'] < 0) {
            $matrix[$current_depth] = _CAT_CUTOFF_CONNECTOR;
        } else {
            // if the current comment doesn't have children
            // and it is at depth ZERO it is an O connector
            // otherwise use a dash connector
            if (!$current_depth) {
                $matrix[$current_depth] = _CAT_O_CONNECTOR;
            } else {
                $matrix[$current_depth] = _CAT_DASH_CONNECTOR;
            }
        }

        // if the current depth is zero then all that it requires is an O or P connector
        // soooo if the current depth is -not- zero then we have other connectors so
        // below we figure out what the other connectors are...
        if (0 != $current_depth) {

            if ( ($current_depth != $prev_depth) ) {
                $matrix[$current_depth - 1] = _CAT_L_CONNECTOR;
            }

            // in order to have a T connector the current depth -must-
            // be less then or equal to the previous depth
            if ( $current_depth <= $prev_depth) {
                // if there is a DepthBuoy set for (current depth -1)
                // then
                if ( categories_renderer_array_depthbuoy('get',($current_depth - 1)) === true ) {
                    // the DepthBuoy for this depth can now be turned off.
                    categories_renderer_array_depthbuoy('set',($current_depth - 1),false);
                    $matrix[($current_depth - 1)] = _CAT_T_CONNECTOR;
                }

                if ($current_depth == $prev_depth) {
                    $matrix[($current_depth - 1)] = _CAT_T_CONNECTOR;
                }

            }

            // Once we've got the T and L connectors done, we need to go through
            // the matrix working our way from the indice equal to the current comment
            // depth towards the begginning of the array - checking for I connectors
            // and Blank connectors.
            for ($node = $current_depth; $node >= 0; ($node = $node - 1)) {

                // be sure not to overwrite another node in the matrix
                if (!$matrix[$node]) {
                    // if a depth buoy was set for this depth, add I connector
                    if (categories_renderer_array_depthbuoy('get',$node) == true) {
                        $matrix[($node)] = _CAT_I_CONNECTOR;
                    } else {  // otherwise add a blank.gif
                        $matrix[($node)] = _CAT_BLANK_CONNECTOR;
                    }
                }
            }
        }

        // Set depth buoy if the next depth is greater then the current,
        // this way we can remember where to set an I connector :)
        if (($next_depth > $current_depth) && ($current_depth != 0)) {
            categories_renderer_array_depthbuoy('set',($current_depth - 1), true);
        }

        // ok -- once that's all done, take this segment of the whole matrix map (ie.,
        // this comment's matrix) create the string of images that will represent this
        // comments place on the "threaded map"...
        $CommentList[$counter]['xar_map'] = implode('', array_map("categories_renderer_array_image_substitution",$matrix));
        //$CommentList[$counter]['xar_map'] = implode('', $matrix);
    }
    return $CommentList;
}


/**
 * Used internally by categories_renderer_array_maptree(). Takes the nodes in a matrix created for
 * a particular comment and translates them into the visual (html'ified) segments of the full map.
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access private
 * @param integer    $matrix    the numerical representation of this segment of the visual map
 * @returns string    a visual (html'ified) map of the matrix
 */

function categories_renderer_array_image_substitution($node)
{

    switch ($node) {
    case _CAT_O_CONNECTOR:
        $map = '<img style="vertical-align: top; border: 0" height="21" src="modules/categories/xarimages/n_nosub.gif" alt="0" width="9" />';
        break;
    case _CAT_P_CONNECTOR:
        $map = '<img style="vertical-align: top; border: 0" height="21" src="modules/categories/xarimages/n_sub.gif" alt="P" width="9" />';
        break;
    case _CAT_T_CONNECTOR:
        $map = '<img style="vertical-align: top; border: 0" height="21" src="modules/categories/xarimages/n_sub_branch_t.gif" alt="t" width="12" />';
        break;
    case _CAT_L_CONNECTOR:
        $map = '<img style="vertical-align: top; border: 0" height="21" src="modules/categories/xarimages/n_sub_branch_l.gif" alt="L" width="12" />';
        break;
    case _CAT_I_CONNECTOR:
        $map = '<img style="vertical-align: top; border: 0" height="21" src="modules/categories/xarimages/n_sub_line.gif" alt="|" width="12" />';
        break;
    case _CAT_BLANK_CONNECTOR:
        $map = '<img style="vertical-align: top; border: 0" height="21" src="modules/categories/xarimages/n_spacer.gif" alt="&nbsp;" width="12"  />';
        break;
    case _CAT_DASH_CONNECTOR:
        $map = '<img style="vertical-align: top; border: 0" height="21" src="modules/categories/xarimages/n_sub_end.gif" alt="_" width="9" />';
        break;
    case _CAT_CUTOFF_CONNECTOR:
        $map = '<img style="vertical-align: top; border: 0" height="21" src="modules/categories/xarimages/n_sub_cutoff.gif" alt="--&gt;" width="9" />';
        break;
    default:
    case _CAT_NO_CONNECTOR:
        $map = '';
        break;
    }
    return $map;
}

/**
 * Used internally by categories_renderer_array_sort(). facilitates
 * sorting of comments whereby the only ones that are sorted in reverse
 * are the top level comments -- all other comments are sorted in ascending order
 * maintaining parent->child relationships
 *
 * @access private
 * @author Carl P. Corliss (aka rabbitt)
 * @param string    $a     Lineage to compare
 * @param string    $b     Lineage to compare
 * @returns integer  -1 if a < b, 0 if a == b, 1 if a > b
 *
 */
function categories_renderer_array_fieldrelation_compare ($a, $b)
{

    // get the sort value
    $sort = categories_renderer_array_sortvalue();

    // first we start off by putting the array key into
    // array format with each id that makes up
    // the lineage having it's own array index.
    // As well, we find out how many id's there
    // are for each Lineage.
    $Family_A = explode(':',$a);
    $Family_A_count = count($Family_A);

    $Family_B = explode(':',$b);
    $Family_B_count = count($Family_B);

    // We need the lineage with the least amount of id's in
    // it for use in our for loop.
    if ($Family_A_count == $Family_B_count) {
        // if they are both equal we could just as easily
        // set this to Family_B instead.. doesn't really
        // matter
        $members_count = $Family_A_count;
    } else {
        $members_count = (($Family_A_count < $Family_B_count)?
                                            $Family_A_count : $Family_B_count);
    }
    // here we do the sorting of the toplevel comments in
    // the list by comparing the first ID's in the lineage
    // which are always the top level id's.
    if (is_numeric($Family_A[0]) && is_numeric($Family_B[0])) {
        if ((int) $Family_A[0] != (int) $Family_B[0]) {

            if ($sort == _CAT_SORT_ASC) {
                return ((int) $Family_A[0] < (int) $Family_B[0]) ? -1 : 1;
            } elseif ($sort == _CAT_SORT_DESC) {
                return ((int) $Family_A[0] < (int) $Family_B[0]) ? 1 : -1;
            } else {
                // in the event that sort is set to some unexpected value
                // assume sort = ASC
                return ((int) $Family_A[0] < (int) $Family_B[0]) ? -1 : 1;
            }
        }
    } else {
        if (strcasecmp($Family_A[0], $Family_B[0]) != 0) {

            if ($sort == _CAT_SORT_ASC) {
                return strcasecmp($Family_A[0], $Family_B[0]);
            } elseif ($sort == _CAT_SORT_DESC) {
                return (int) -(strcasecmp($Family_A[0], $Family_B[0]));
            } else {
                // in the event that sort is set to some unexpected value
                // assume sort = ASC
                return strcasecmp($Family_A[0], $Family_B[0]);
            }
        }
    }
    // now we do an id to id comparison but only up to the number of
    // elements (comment ids) of the smallest lineage.
    for ($i = 1; $i < $members_count; $i++) {
        if ((int) $Family_A[$i] != (int) $Family_B[$i]) {
            return ((int) $Family_A[$i] < (int) $Family_B[$i]) ? -1 : 1;
        }
    }

    // Since we are here it means that both lineages matched up to the
    // length of the smallest lineage soo-, the one that has the most
    // elements (comment ids) is obviously of higher value. If however they
    // have the same amount of elements, then the lineages are the same --
    // [Note]: this should NEVER happen.
    if ($Family_A_count != $Family_B_count) {
        return ($Family_A_count < $Family_B_count) ? -1 : 1;
    } else {
        return 0;
    }
}

/**
 * Used to set/retrieve the current value of sort. -- used internally
 * and should not be utilized outside of this function group.
 *
 * @access  private
 * @author  Carl P. Corliss (aka rabbitt)
 * @param   string  $value  'ASC' for Ascending, 'DESC' for descending sort order
 * @returns  string  The current sort value
 *
 */
function categories_renderer_array_sortvalue($value=NULL)
{
    static $sort;

    if ($value != NULL) {
        switch (strtolower($value)) {
        case _CAT_SORT_DESC:
            $sort = _CAT_SORT_DESC;
            break;
        case _CAT_SORT_ASC:
        default:
            $sort = _CAT_SORT_ASC;
        }
    }
    return $sort;
}

/**
 * Sorts the specified array by the specified 'sortby' value in the direction specified by 'direction'
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access public
 * @param    string  $args['sortby']         represents the field to sort by
 * @param    string  $args['direction']      represents the direction to sort (ascending / descending )
 * @param    array   $args['comment_list']   List of comments to sort
 * @returns   void    nothing
 */

function  categories_renderer_array_sort( &$comment_list, $sortby, $direction)
{

    if (!isset($comment_list) || !is_array($comment_list)) {
        $msg = xarML('Missing or invalid arguement [#(1)] for #(2) function #(3) in module #(4)',
                                 'comment_list','renderer','array_sort','comments');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException(__FILE__.' ('.__LINE__.'):  '.$msg));
        return false;
    }

    $index      = array();
    $new_list   = array();

    categories_renderer_array_sortvalue($direction);

    if ($sortby == _CAT_SORTBY_THREAD) {
        foreach ($comment_list as $node) {

            if ($node['depth'] == 0) {
                $key = $node['xar_cid'];
                $index[$node['xar_cid']] = $key;
            } else {
                $key = $index[$node['xar_pid']] .":".$node['xar_cid'];
                $index[$node['xar_cid']] = $key;
            }
            $new_list[$key] = $node;
        }
    } else {
        // Initial presort for non threaded sort - We do a presort to
        // get all the comments in order by the key that we're sorting
        // by -- otherwise, when we assign parents and children
        // (further below) there will  be a chance that some will be
        // out of order and mess up the rendering
        foreach ($comment_list as $node) {
            switch($sortby) {
                case _CAT_SORTBY_TOPIC:
                    $key = eregi_replace("\:"," ",$node['xar_subject']);
                    break;
                case _CAT_SORTBY_DATE:
                    $key = 'a' . $node['xar_datetime'];
                    break;
                default:
                case _CAT_SORTBY_AUTHOR:
                    $key = $node['xar_author'];
                    break;
                    // default to sorting by author
            }

            $new_list[$key .":". $node['xar_cid']] = $node;
        }
        $comment_list = $new_list;
        $new_list = array();

        uksort($comment_list, 'categories_renderer_array_fieldrelation_compare');
        // End of PreSORT

        foreach ($comment_list as $node) {
            switch($sortby) {
                case _CAT_SORTBY_TOPIC:
                    $key = eregi_replace("\:"," ",$node['xar_subject']);
                    break;
                case _CAT_SORTBY_DATE:
                    $key = 'a' . $node['xar_datetime'];
                    break;
                default:
                case _CAT_SORTBY_AUTHOR:
                    $key = $node['xar_author'];
                    break;
                    // default to sorting by author
            }

            if (!isset($index[$key])) {
                $index[$key]['depth'] = 0;
                $index[$key]['children'] = 0;
                $new_list[$key.":0"] = $node;
                $new_list[$key.":0"]['depth'] = $index[$key]['depth'];
                $new_list[$key.":0"]['children'] = $index[$key]['children'];
            } else {
                $key2 = $key.":".$node['xar_cid'];
                $new_list[$key2] = $node;
                $new_list[$key2]['depth'] = 1;
                $new_list[$key2]['children'] = 0;
                $new_list[$key.":0"]['children'] += 1;
            }
        }
    }
    $comment_list = $new_list;

    uksort($comment_list, 'categories_renderer_array_fieldrelation_compare');

    // reset the indexes on the comments_list
    $comments = array();
    foreach ($comment_list as $comment) {
        $comments[] = $comment;
    }

    $comment_list = $comments;
    unset($comments);

    return $comment_list;
}

function categories_renderer_wrap_words(&$str, $chars)
{
    $str = preg_replace('/([^\s\<\>]{'.$chars.','.$chars.'})/', '\1 ', $str);
}


?>

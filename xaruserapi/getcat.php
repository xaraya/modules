<?php
/**
 * Categories module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Categories Module
 * @link http://xaraya.com/index.php/release/147.html
 * @author Categories module development team
 */
/**
 * get categories
 *
 * @param int $args['cid'] restrict output only to this category ID and its sibbling (default none)
 * @param int $args['eid'] do not output this category and its sibblings (default none)
 * @param int $args['maximum_depth'] return categories with the given depth or less
 * @param int $args['minimum_depth'] return categories with the given depth or more
 * @param $args['indexby'] =string= specify the index type for the result array (default 'default')
 *  They only change the output IF 'cid' is set:
 *    @param $args['getchildren'] =Boolean= get children of category (default false)
 *    @param $args['getparents'] =Boolean= get parents of category (default false)
 *    @param $args['return_itself'] =Boolean= return the cid itself (default false)
 * @return array Array of categories, or =Boolean= false on failure

 * Examples:
 *    getcat() => Return all the categories
 *    getcat(Array('cid' -> ID)) => Only cid and its children, grandchildren and
 *                                   every other sibbling will be returned
 *    getcat(Array('eid' -> ID)) => All categories will be returned EXCEPT
 *                                   eid and its children, grandchildren and
 *                                   every other sibbling will be returned
 */
function categories_userapi_getcat($args)
{
    extract($args);

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    if (!isset($return_itself)) {
        $return_itself = false;
    }

    if (empty($indexby)) {$indexby = 'default';}

    if (!isset($getchildren)) {
        $getchildren = false;
    }
    if (!isset($getparents)) {
        $getparents = false;
    }
    if (!isset($start)) {
        $start = 0;
    }
    elseif (!is_numeric($start)) {
        xarSessionSetVar('errormsg', xarML('Bad numeric arguments for API function'));
        return false;
    } else {
        //The pager starts counting from 1
        //SelectLimit starts from 0
        $start--;
    }
    if (!isset($count)) {
        $count = 0;
    }
    elseif (!is_numeric($count)) {
        xarSessionSetVar('errormsg', xarML('Bad numeric arguments for API function'));
        return false;
    }

    $categoriestable = $xartable['categories'];
    $bindvars = array();
    $SQLquery = "SELECT
                        COUNT(P2.xar_cid) AS indent,
                        P1.xar_cid,
                        P1.xar_name,
                        P1.xar_description,
                        P1.xar_image,
                        P1.xar_parent,
                        P1.xar_left,
                        P1.xar_right
                   FROM $categoriestable P1,
                        $categoriestable P2
                  WHERE P1.xar_left  >= P2.xar_left
                    AND P1.xar_left  <= P2.xar_right";
/* this is terribly slow, at least for MySQL 3.23.49-nt
                  WHERE P1.xar_left
                BETWEEN P2.xar_left AND
                        P2.xar_right";
*/
    if (isset($cid) && !is_array($cid) && $cid != false)
    {
        if ($getchildren || $getparents)
        {
            // We have the category ID but we need
            // to know its left and right values
            $cat = xarModAPIFunc('categories','user','getcatinfo',Array('cid' => $cid));
            if ($cat == false) {
                xarSessionSetVar('errormsg', xarML('Category does not exist'));
                return Array();
            }

            // If not returning itself we need to take the appropriate
            // left values
            if ($return_itself)
            {
                $return_child_left = $cat['left'];
                $return_parent_left = $cat['left'];
            }
            else
            {
                $return_child_left = $cat['left'] + 1;
                $return_parent_left = $cat['left'] - 1;
            }

            // Introducing an AND operator in the WHERE clause
            $SQLquery .= ' AND (';
        }

        if ($getchildren)
        {
            $SQLquery .= "(P1.xar_left BETWEEN ? AND ?)";
            $bindvars[] = $return_child_left; $bindvars[] = $cat['right'];
        }

        if ($getparents && $getchildren)
        {
               $SQLquery .= " OR ";
        }

        if ($getparents)
        {
             $SQLquery .= "( ? BETWEEN P1.xar_left AND P1.xar_right)";
            $bindvars[] = $return_parent_left;
        }

        if ($getchildren || $getparents)
        {
            // Closing the AND operator
            $SQLquery .= ' )';
        }
        else
        {// !(isset($getchildren)) && !(isset($getparents))
            // Return ONLY the info about the category with the given CID
            //@todo: if we know this early, put it in front, so we limit the records early on
            $SQLquery .= " AND (P1.xar_cid = ?) ";
            $bindvars[] = $cid;
        }

    }

    if (isset($eid) && !is_array($eid) && $eid != false) {
       $ecat = xarModAPIFunc('categories', 'user', 'getcatinfo', Array('cid' => $eid));
       if ($ecat == false) {
           xarSessionSetVar('errormsg', xarML('That category does not exist'));
           return Array();
       }
       //$SQLquery .= " AND P1.xar_left
       //               NOT BETWEEN ? AND ? ";
       $SQLquery .= " AND (P1.xar_left < ? OR P1.xar_left > ?)";
       $bindvars[] = $ecat['left']; $bindvars[] = $ecat['right'];
    }

    // Have to specify all selected attributes in GROUP BY
    $SQLquery .= " GROUP BY P1.xar_cid, P1.xar_name, P1.xar_description, P1.xar_image, P1.xar_parent, P1.xar_left, P1.xar_right ";

    $having = array();
    // Postgre doesnt accept the output name ('indent' here) as a parameter in the where/having clauses
    // Bug #620
    if (isset($minimum_depth) && is_numeric($minimum_depth)) {
        $having[] = "COUNT(P2.xar_cid) >= ?";
        $bindvars[] = $minimum_depth;
    }
    if (isset($maximum_depth) && is_numeric($maximum_depth)) {
        $having[] = "COUNT(P2.xar_cid) < ?";
        $bindvars[] = $maximum_depth;
    }
    if (count($having) > 0) {
// TODO: make sure this is supported by all DBs we want
        $SQLquery .= " HAVING " . join(' AND ', $having);
    }

    $SQLquery .= " ORDER BY P1.xar_left";

// cfr. xarcachemanager - this approach might change later
    $expire = xarModGetVar('categories','cache.userapi.getcat');
    if (is_numeric($count) && $count > 0 && is_numeric($start) && $start > -1) {
        if (!empty($expire)){
            $result = $dbconn->CacheSelectLimit($expire,$SQLquery, $count, $start, $bindvars);
        } else {
            $result = $dbconn->SelectLimit($SQLquery, $count, $start, $bindvars);
        }
    } else {
        if (!empty($expire)){
            $result = $dbconn->CacheExecute($expire,$SQLquery,$bindvars);
        } else {
            $result = $dbconn->Execute($SQLquery, $bindvars);
        }
    }

    if (!$result) return;

    if ($result->EOF) {
        //It?s ok.. no category found
        // The user doesn?t need to be informed, he will see it....
//        xarSessionSetVar('statusmsg', xarML('No category found'));
        return Array();
    }

    $categories = Array();

    $index = -1;
    while (!$result->EOF) {
        list($indentation,
                $cid,
                $name,
                $description,
                $image,
                $parent,
                $left,
                $right
               ) = $result->fields;
        $result->MoveNext();

        if (!xarSecurityCheck('ViewCategories',0,'Category',"$name:$cid")) {
             continue;
        }

        if ($indexby == 'cid') {
            $index = $cid;
        } else {
            $index++;
        }

        $categories[$index] = Array(
            'indentation' => $indentation,
            'cid'         => $cid,
            'name'        => $name,
            'description' => $description,
            'image'       => $image,
            'parent'      => $parent,
            'left'        => $left,
            'right'       => $right
        );
    }
    $result->Close();

    return $categories;
}

?>

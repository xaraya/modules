<?php

/**
 * get categories
 *
 * @param $args['cid'] =Integer= restrict output only to this category ID and its sibbling (default none)
 * @param $args['eid'] =Integer= do not output this category and its sibblings (default none)
 * @param $args['maximum_depth'] =Integer= return categories with the given depth or less
 * @param $args['minimum_depth'] =Integer= return categories with the given depth or more
 *  They only change the output IF 'cid' is set:
 *    @param $args['getchildren'] =Boolean= get children of category (default false)
 *    @param $args['getparents'] =Boolean= get parents of category (default false)
 *    @param $args['return_itself'] =Boolean= return the cid itself (default false)
 * @return =Array= of categories, or =Boolean= false on failure

 * Examples:
 *    getcat() => Return all the categories
 *    getcat(Array('cid' -> ID)) => Only cid and its children, grandchildren and
 *                                   every other sibbling will be returned
 *    getcat(Array('eid' -> ID)) => All categories will be returned EXCEPT
 *                                   eid and its children, grandchildren and
 *                                   every other sibbling will be returned
 */
function categories_userapi_getcat($args) {
    extract($args);

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    if (!isset($return_itself)) {
        $return_itself = false;
    }

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

    $SQLquery = "SELECT
                        COUNT(P2.xar_cid) AS indent,
                        P1.xar_cid,
                        P1.xar_name,
                        P1.xar_description,
                        P1.xar_image,
                        P1.xar_parent,
                        P1.xar_left,
                        P1.xar_right
                   FROM $categoriestable AS P1,
                        $categoriestable AS P2
                  WHERE P1.xar_left
                     >= P2.xar_left
                    AND P1.xar_left
                     <= P2.xar_right";
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
          $SQLquery .= "(P1.xar_left BETWEEN ".$return_child_left." AND ".$cat['right'].")";
        }

        if ($getparents && $getchildren)
        {
               $SQLquery .= " OR ";
        }

        if ($getparents)
        {
             $SQLquery .= "(".$return_parent_left ." BETWEEN P1.xar_left AND P1.xar_right)";
        }

        if ($getchildren || $getparents)
        {
            // Closing the AND operator
            $SQLquery .= ' )';
        }
        else
        {// !(isset($getchildren)) && !(isset($getparents))
            // Return ONLY the info about the category with the given CID
            $SQLquery .= " AND (P1.xar_cid = ".xarVarPrepForStore($cid).") ";
        }

    }

    if (isset($eid) && !is_array($eid) && $eid != false) {
       $ecat = xarModAPIFunc('categories', 'user', 'getcatinfo', Array('cid' => $eid));
       if ($ecat == false) {
           xarSessionSetVar('errormsg', xarML('That category does not exist'));
           return Array();
       }
       $SQLquery .= " AND P1.xar_left
                      NOT BETWEEN ".$ecat['left']." AND ".$ecat['right']." ";
    }

    // Have to specify all selected attributes in GROUP BY
    $SQLquery .= " GROUP BY P1.xar_cid, P1.xar_name, P1.xar_description, P1.xar_image, P1.xar_parent, P1.xar_left, P1.xar_right ";

    $having = array();
    // Postgre doesnt accept the output name ('indent' here) as a parameter in the where/having clauses
    // Bug #620
    if (isset($minimum_depth) && is_numeric($minimum_depth)) {
        $having[] = "COUNT(P2.xar_cid) >= " . $minimum_depth;
    }
    if (isset($maximum_depth) && is_numeric($maximum_depth)) {
        $having[] = "COUNT(P2.xar_cid) < " . $maximum_depth;
    }
    if (count($having) > 0) {
// TODO: make sure this is supported by all DBs we want
        $SQLquery .= " HAVING " . join(' AND ', $having);
    }

    $SQLquery .= " ORDER BY P1.xar_left";

    if (is_numeric($count) && $count > 0 && is_numeric($start) && $start > -1) {
       $result = $dbconn->SelectLimit($SQLquery, $count, $start);
    } else {
       $result = $dbconn->Execute($SQLquery);
    }

    if (!$result) return;

    if ($result->EOF) {
        //It´s ok.. no category found
        // The user doesn´t need to be informed, he will see it....
//        xarSessionSetVar('statusmsg', xarML('No category found'));
        return Array();
    }

    $categories = Array();

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
/*
        // FIXME: Move max/min depth into the SQL
        if ((
             (!isset($minimum_depth)) ||
             ($indentation >= $minimum_depth)
            ) && (
              (!isset($maximum_depth)) ||
             ($indentation <= $maximum_depth)
           ))
        {
*/
            $categories[] = Array(
                                  'indentation' => $indentation,
                                  'cid'         => $cid,
                                  'name'        => $name,
                                  'description' => $description,
                                  'image'       => $image,
                                  'parent'      => $parent,
                                  'left'        => $left,
                                  'right'       => $right
                               );
/*
        }
*/
    }
    $result->Close();

    return $categories;
}

?>

<?php

define('_NEWS_SORT_ASC', 'asc');
define('_NEWS_SORT_DESC', 'desc');

function newsgroups_userapi_create_threads( $articles , $direction=_NEWS_SORT_DESC) 
{

    if (!is_array($articles)) {
        return $articles;
    }

    array_sortvalue($direction);
    $new_list = array();

    foreach ($articles as $id => $article) {
        unset($keytime);
        $refs = array();

        if (isset($article['References'])) {
            $refs = explode(' ',$article['References']);
        }

        $article['depth'] = count($refs) - 1;
        array_push($refs, $id);

        foreach ($refs as $key => $refid) {

            if ($refid != $id) {
                $refid = md5($refid);
                if (isset($articles[$refid])) {
                    $articles[$refid]['isparent'] = true;
                    if (!isset($keytime) || empty($keytime)) {
                        $keytime = strtotime($articles[$refid]['Date']);
                    }
                }
            }

            if (!isset($keytime) || empty($keytime)) {
                $keytime = strtotime($article['Date']);
            }
            $refs[$key] = $refid;
        }


        $key = $keytime.':'.implode(':',$refs);

        // echo "\n<Br /><pre>$key\n\t\t"; print_r($refs); echo "</pre><br />\n";
        $new_list[$key] = $article;
    }
    unset($articles);

    uksort($new_list, 'array_fieldrelation_compare');

    return $new_list;

}

/**
 * Used internally by array_sort(). facilitates
 * sorting of newsgroups whereby the only ones that are sorted in reverse
 * are the top level newsgroups -- all other newsgroups are sorted in ascending order
 * maintaining parent->child relationships
 *
 * @access private
 * @author Carl P. Corliss (aka rabbitt)
 * @param string    $a     Lineage to compare
 * @param string    $b     Lineage to compare
 * @returns integer  -1 if a < b, 0 if a == b, 1 if a > b
 *
 */
function array_fieldrelation_compare ($a, $b) 
{

    // get the sort value
    $sort = array_sortvalue();

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
    // here we do the sorting of the toplevel newsgroups in
    // the list by comparing the first ID's in the lineage
    // which are always the top level id's.
    if (is_numeric($Family_A[0]) && is_numeric($Family_B[0])) {
        if ((int) $Family_A[0] != (int) $Family_B[0]) {

            if ($sort == _NEWS_SORT_ASC) {
                return ((int) $Family_A[0] < (int) $Family_B[0]) ? -1 : 1;
            } elseif ($sort == _NEWS_SORT_DESC) {
                return ((int) $Family_A[0] < (int) $Family_B[0]) ? 1 : -1;
            } else {
                // in the event that sort is set to some unexpected value
                // assume sort = ASC
                return ((int) $Family_A[0] < (int) $Family_B[0]) ? -1 : 1;
            }
        }
    } else {
        if (strcasecmp($Family_A[0], $Family_B[0]) != 0) {

            if ($sort == _NEWS_SORT_ASC) {
                return strcasecmp($Family_A[0], $Family_B[0]);
            } elseif ($sort == _NEWS_SORT_DESC) {
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
function array_sortvalue($value=NULL) 
{
    static $sort;

    if ($value != NULL) {
        switch (strtolower($value)) {
        case _NEWS_SORT_DESC:
            $sort = _NEWS_SORT_DESC;
            break;
        case _NEWS_SORT_ASC:
        default:
            $sort = _NEWS_SORT_ASC;
        }
    }
    return $sort;
}


?>
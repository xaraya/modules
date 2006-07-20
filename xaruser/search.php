<?php
/**
 * Example Module User Search
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */
/**
 * Search for an example item
 *
 * This function is called via the search hook
 *
 * @author Jo Dalle Nogare
 * @return array with the items found, or a string explaining that nothing was found
 */
function example_user_search()
{
    /* Required search hook fields */
    if (!xarVarFetch('q',         'isset',  $q,        NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('bool',      'isset',  $bool,     NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('sort',      'isset',  $sort,     NULL, XARVAR_DONT_SET)) return;

    /* Standard Pager information */
    if(!xarVarFetch('startnum', 'int:0', $startnum,  NULL, XARVAR_NOT_REQUIRED)) {return;}

    /* example module fields for possible searching and identification */
    if (!xarVarFetch('name',   'str:0:', $name,   '',   XARVAR_DONT_SET)) return;
    if (!xarVarFetch('number', 'int:0:', $number, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('exid',       'id', $exid,   NULL, XARVAR_NOT_REQUIRED)) return;

    /* example for search where an author is involved, not included in this example module */
    /* if(!xarVarFetch('author', 'isset',  $author,   NULL, XARVAR_DONT_SET)) {return;}
     */

    $data       = array();
    $search     = array();

    if($q == ''){
        return $data;
    }
    /* Set some defaults here for the search and search result display */
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = 10;
    }

    /* Your module may need searching by more 'complex' fields such as author
     * and if so you might include some similar code as below
     */
     /*
    if (!isset($q) || strlen(trim($q)) <= 0) {
        if (isset($author) && strlen(trim($author)) > 0) {
            $q = $author;
            $search['author']=$author;
            $data['authorsearch']=1;
        }
    } else {
        $search['author']='';
        $data['authorsearch']=1;
    }
    */


    /* Setup the data for the search form for the checkboxes and
     * and other information needed for display
     */

    if (isset($exid)) {
        $search['exid'] = $q;
        $data['exid']=1;
    } else {
        $data['exid']=0;
        $exid=0;
    }
    if (isset($name)) {
        $search['name'] = $q;
        $data['name']=1;
    } else {
        $data['name']=0;
        $name='';
    }
    if (isset($number)) {
        $search['number'] = $q;
        $data['number']=1;
    } else {
        $data['number']=0;
        $number=0;
    }

    /* Example code that you might use to find the uid of the author we're looking for
     * Adjust for your own module if required. Demonstrates use of roles api call
     * rather than direct db function to protect against roles table changes for example
     */

    /*if (!empty($author)) {
        // Load API
        if (!xarModAPILoad('roles', 'user')) return;
        $user = xarModAPIFunc('roles','user','get',
                             array('name' => $author));
        if (!empty($user['uid'])) {
            $search['authorid'] = $user['uid'];
        } else {
            $search['authorid']= null;
            $search['author'] = null;
        }
    } else {
        $search['authorid'] = null;
        $search['author'] = null;
    }
    */
    $search['q']=$q;

    /* Call search hook for example module to Search example information */
    if (!empty($q) || !empty($search)) {
        $data['example'] = xarModAPIFunc('example','user','search',$search);
    }
    /* Prepare the message to return to search template if no match found */
    if (empty($data['example'])){
        $data['status'] = xarML('No Example item found that matches your search');
    }
    /* Return the results to the search hook */
    return $data;
}
?>

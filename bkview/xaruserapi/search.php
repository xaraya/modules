<?php

/**
 * File: $Id$
 *
 * Search handler for bkview module
 *
 * This is the item:search:api hook function for the bkview 
 * module. Each kind of this function is supposed to do its
 * search thing specific for it's containing module and return
 * an array of search-results with keys: result, link, itemtype and optionally 
 * description. The search module then renders that array in it's search
 * results template.
 *
 * Each search api function gets two parameters from the search module: terms
 * and itemtypes. The latter is an array which contains the itemtypes the
 * user wants to search in. The module itself should know what to do with these
 *
 * So:
 * $object_id -> when not 0 a specific repository was specified
 * $terms -> string with entered search terms
 * $itemtypes = array('itemtypename' => itemtypeid, ... ,)
 * 
 * $results = array(
 *                  array('result' => string describing the result,
 *                        'link'   => link to the result,
 *                        'itemtype' => in which itemtype was this found (text, not id)
 *                        'description' => longer result text
 *                       )
 *                  ...
 *                 )                 
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

include_once("modules/bkview/xarincludes/bk.class.php");

function bkview_userapi_search($args) {
    extract($args);

    if($object_id) {
        $repoinfo = xarModAPIFunc('bkview','user','get',array('repoid' => $object_id));
        $repo = new bkRepo($repoinfo['repopath']);
    }

    // Now we now which repo to search
    $searchresults = array();
    if(!empty($itemtypes)) {
        // item types where specified, do the search for them
        foreach($itemtypes as $itemtype_name => $itemtype_id) {
            $itemtype_results = array();
            switch($itemtype_id) {
                case BK_ITEMTYPE_REPO:
                    // Search repository information
                    $itemtype_results = _bk_search_repoinfo($repo, $terms);
                    break;
                case BK_ITEMTYPE_FILE:
                    // Search file contents
                    $itemtype_results = _bk_search_files($repo, $terms);
                    break;
                case BK_ITEMTYPE_CSET:
                    // Search cset comments
                    $itemtype_results = _bk_search_csets($repo, $terms);
                    break;
                case BK_ITEMTYPE_DELTA:
                    // Search delta comments
                    $itemtype_results = _bk_search_deltas($repo, $terms);
                    break;
            }
            $searchresults = array_merge($searchresults, $itemtype_results);
        }
    } else {
        // should we search *all* or *nothing* here?
    }
    return $searchresults;
   
}

function _bk_search_repoinfo($repo, $terms) {
    return array(array('result' => 'dummy repoinfo search result',
                       'link'   => xarModUrl('bkview','user','view'),
                       'itemtype' => 'Repo info',
                       'description' => 'Dummy search result for searching repository information'
                       )
                 );
}

function _bk_search_files($repo, $terms) {
    return array(array('result' => 'dummy file content search result',
                       'link'   => xarModUrl('bkview','user','view'),
                       'itemtype' => 'Filecontent',
                       'description' => 'Dummy search result for searching file contents in a repository'
                       )
                 );
}

function _bk_search_csets($repo, $terms) {
    return array(array('result' => 'dummy cset comments search result',
                       'link'   => xarModUrl('bkview','user','view'),
                       'itemtype' => 'Cset comments',
                       'description' => 'Dummy search result for searching cset comments in a repository'
                       )
                 );
}

function _bk_search_deltas($repo, $terms) {
    return array(array('result' => 'dummy delta comments search result',
                 'link'   => xarModUrl('bkview','user','view'),
                 'itemtype' => 'Delta comments')
                 );
}

?>

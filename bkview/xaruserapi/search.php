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

    // We can't do anything without an object id, fail silently by
    // returning an empty array
    if(empty($object_id)) return array();

    $repoinfo = xarModAPIFunc('bkview','user','get',array('repoid' => $object_id));
    $repo = new bkRepo($repoinfo['repopath']);


    // Now we now which repo to search
    $searchresults = array();
    if(!empty($itemtypes)) {
        // item types where specified, do the search for them
        foreach($itemtypes as $itemtype_name => $itemtype_id) {
            $itemtype_results = array();
            switch($itemtype_id) {
                case BK_ITEMTYPE_REPO:
                    // Search repository information
                    $itemtype_results = _bk_search_repoinfo($repo, $terms,$object_id);
                    break;
                case BK_ITEMTYPE_FILE:
                    // Search file contents
                    $itemtype_results = _bk_search_files($repo, $terms,$object_id);
                    break;
                case BK_ITEMTYPE_CSET:
                    // Search cset comments
                    $itemtype_results = _bk_search_csets($repo, $terms,$object_id);
                    break;
                case BK_ITEMTYPE_DELTA:
                    // Search delta comments
                    $itemtype_results = _bk_search_deltas($repo, $terms,$object_id);
                    break;
            }
            $searchresults = array_merge($searchresults, $itemtype_results);
        }
    } else {
        // should we search *all* or *nothing* here?
    }
    return $searchresults;
   
}

function _bk_search_repoinfo($repo, $terms,$object_id) {
    return array(array('result' => 'dummy repoinfo search result',
                       'context' => 'This contains some context of the result found, so we can higlight the search terms',
                       'link'   => xarModUrl('bkview','user','view'),
                       'itemtype' => 'Repo info',
                       'description' => 'Dummy search result for searching repository information'
                       )
                 );
}

function _bk_search_files($repo, $terms,$object_id) {
    return array(array('result' => 'dummy file content search result',
                       'context' => 'This contains some context of the result found, so we can higlight the search terms',
                       'link'   => xarModUrl('bkview','user','view'),
                       'itemtype' => 'Filecontent',
                       'description' => 'Dummy search result for searching file contents in a repository'
                       )
                 );
}

function _bk_search_csets($repo, $terms,$object_id) {
    // Search cset comments in $repo for $terms
    // basic command is: bk prs -h -d'$each(:C:){:I:(:C:)}\n' ChangeSet | grep 'blah'
    $matches = $repo->bkSearch($terms);
    $itemtype = xarML('Changeset comment');
    $dots = xarML('...');
    $results = array();
    foreach($matches as $match) {
        list($rev,$comment) = explode('|', $match);
        $result = array('result' => xarML('Changeset') . ' ' . $rev,
                        'context' => $dots . substr($comment,0,80) . $dots,
                        'link' => xarModUrl('bkview','user','deltaview', array('rev' => $rev, 'repoid' => $object_id)),
                        'itemtype' => $itemtype,
                        'description' => $comment
                        );
        $results[] = $result;
    }
    return $results;
}

function _bk_search_deltas($repo, $terms,$object_id) {
    return array(array('result' => 'dummy delta comments search result',
                       'context' => 'This contains some context of the result found, so we can higlight the search terms',
                       'link'   => xarModUrl('bkview','user','view'),
                       'itemtype' => 'Delta comments')
                 );
}

?>

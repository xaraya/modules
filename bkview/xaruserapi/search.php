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

function bkview_userapi_search($args) 
{
    extract($args);

    // We can't do anything without an object id, fail silently by
    // returning an empty array
    if(empty($object_id)) return array();

    $repoinfo = xarModAPIFunc('bkview','user','get',array('repoid' => $object_id));
    $repo =& $repoinfo['repo'];

    // Now we now which repo to search
    $searchresults = array();
    $itemtype_results = array();

    if(!empty($itemtypes)) {
        // item types where specified, do the search for them
        foreach($itemtypes as $itemtype_name => $itemtype_id) {
            $itemtype_results = _bk_search($repo, $terms,$object_id,$itemtype_id);
            $searchresults = array_merge($searchresults, $itemtype_results);
        }
    }
    xarLogVariable('searchresults',$searchresults);
    return $searchresults;
   
}

function _bk_search($repo, $terms,$object_id,$itemtype_id) 
{
    // Get the raw data
    $matches = $repo->bkSearch($terms,$itemtype_id);
    $results=array();
    $dots = xarML('...');
    $revision = xarML('revision'); $changeset = xarML('ChangeSet');

    foreach($matches as $match) {
        switch($itemtype_id) {
        case BK_ITEMTYPE_REPO:
            $itemtype = xarML('Repo info');
            $link = xarModUrl('bkview','user','view');
            $result = 'dummy repoinfo search result';
            $context = 'This contains some context of the result found, so we can higlight the search terms';
            $description = 'Dummy search result for searching repository information';
            break;
        case BK_ITEMTYPE_FILE:
            $match = str_replace("\t",'|',$match);
            list($file, $rev, $comment) = explode("|",$match);
            $itemtype = xarML('File content');
            $result = $file . ' @ ' . $revision . ' ' . $rev;
            $link = xarModUrl('bkview','user','annotateview',array('repoid' => $object_id,'rev' => $rev,'file' => $file));
            $context =  $dots . substr($comment,0,80) . $dots;
            $description = $comment;
            break;
        case BK_ITEMTYPE_CSET:
            list($rev,$comment) = explode('|', $match);
            $itemtype = xarML('Changeset comment');
            $result = $changeset . ' ' . $rev;
            $context = $dots . substr($comment,0,80) . $dots;
            $link = xarModUrl('bkview','user','deltaview', array('rev' => $rev, 'repoid' => $object_id));
            $description = $comment;
            break;
        case BK_ITEMTYPE_DELTA:
            list($file, $rev, $comment) = explode('|', $match);
            $itemtype = xarML('File delta comment');
            $result = $file . ' @ ' . xarML('revision') . $rev;
            $context = $dots . substr($comment,0,80) . $dots;
            $link = xarModUrl('bkview','user','diffview',array('repoid' => $object_id,'rev' => $rev,'file' => $file));
            $description = $comment;
            break;
        }
        $results[] = array('result'      => $result,
                           'context'     => $context, 
                           'link'        => $link,
                           'itemtype'    => $itemtype,
                           'description' => $description
                           );
    }

    return $results;

}

?>

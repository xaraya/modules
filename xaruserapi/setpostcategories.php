<?php
/**
 * Moveable type module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage moveabletype
 * @author Marcel van der Boom <marcel@xaraya.com>
 */
function moveabletype_userapi_setpostcategories($args)
{
    xarLogMessage('Moveabletype api: setpostcategories', XARLOG_LEVEL_WARNING);
    extract($args);

    // get the params
    $sn1=$msg->getParam(0);  $postid        = $sn1->scalarval(); // NOTE: NOT blogid
    $sn2=$msg->getParam(1);  $username      = $sn2->scalarval();
    $sn3=$msg->getParam(2);  $password      = $sn3->scalarval();
    $sn4=$msg->getParam(3);  $categories    = $sn4->getVal();

    // Try to login
    $err='';
    if (empty($password) || !xarUserLogin($username,$password)) {
        $err = xarML("Invalid username or password for (#(1)) while getting recent posts",$username);
    } else {
        $itemType=xarModGetVar('bloggerapi','bloggerpubtype');
        // We do DO want to include the main category here, although that is XAR implementation on NEW,
        // it is required because we do a clean_first in the category linking below
        // First get the root categories of the publication type (as in: all the blogs)
        $rootCats = array();
        $rootCats = xarModGetVar('articles','mastercids.'.$itemType);
        if (!empty($rootCats)) $rootCats = explode(';',$rootCats);

        // Now get the categories for the specific post of the specific post
        $itemCats = xarModAPIFunc('categories', 'user','getitemcats',array('modid'=> 151, 'itemtype' => $itemType, 'itemid'   => $postid));
        $itemCats = array_keys($itemCats);

        // The cat we want is the one which is in both
        $cids = array_intersect($rootCats, $itemCats);

        // Get the other designated categories from the xml request
        if(!empty($categories)) {
            // Match the names we got from the client to ids, we only have to consider the subcats of blogid
            foreach($categories as $catStruct) {
                // w.bloggar apparently sends -1 for no changes, so lets check this
                if( isset($catStruct['categoryId']) && is_object($catStruct['categoryId']) ) {
                    $catId = intval($catStruct['categoryId']->scalarval());
                    if($catId > 0) $cids[] = $catId;
                }

                if(isset($catStruct['isPrimary']) && is_object($catStruct['isPrimary'])) {
                    $isPrimary = $catStruct['isPrimary']->scalarval(); // TODO: Where do we put this in Xaraya?
                }
            }
        }
        // $cids now contains all the cat ids to which we should link $aid, if any
        $result = xarModAPIFunc('categories','admin','linkcat', array('cids' => $cids,
                                                                      'iids' => array($postid),
                                                                      'modid' => 151, // articles
                                                                      'itemtype' => $itemType,
                                                                      'clean_first' => true));
        if(!$result) $err = xarML('A problem occurred while setting the categories for this entry');
    }

    // Return the right response
    if(empty($err)) {
        $output = xarModAPIFunc('xmlrpcserver','user','successresponse');
    } else {
        $output = xarModAPIFunc('xmlrpcserver','user','faultresponse',array('errorstring' => $err));
    }
    return $output;
}
?>
<?php

/**
 * $Id$
 *
 * Implementation of the metaWeblog.getCategories method
 *
 *
 * For blogging we use the xaraya categories as the entry point for
 * a blog in combination with a publication type. 
 * The categories which are mentioned here are the categories, which will be
 * attached to the posting through the weblog api, which is something different.
 * Ideally each weblog should be able to have a set of categories of its own,
 * so what we ideally want to do, is hook categories into categories.
 *
 */

function metaweblogapi_userapi_getcategories($args)
{
    xarLogMessage('MetaWeblog api: getcategories', XARLOG_LEVEL_WARNING);
    extract($args);
    
    // get the params
    $sn1=$msg->getParam(0);  $blogid        = $sn1->scalarval();
    $sn2=$msg->getParam(1);  $username      = $sn2->scalarval();
    $sn3=$msg->getParam(2);  $password      = $sn3->scalarval();

    // Try to login 
    $err='';
    $elements = array();
    if (!xarUserLogin($username,$password)) {
        $err = xarML("Invalid username or password for (#(1)) while getting recent posts",$username);
    } else {
        // Based on the $blog id, which is the category id of this blog, get its
        // subcategories, which are interpreted as the categories in the weblog
        if(xarModIsAvailable('categories')) {
            $childcats = xarModAPIFunc('categories', 'user', 'getcat',
                                    array('return_itself'=>false,'getchildren'=>true,'cid'=> $blogid));
        } else {
            $err = xarML("The categories module is not available, this is required");
        }
        
        $pubtype=xarModGetVar('bloggerapi','bloggerpubtype');
        if(!empty($childcats)) {
            // Construct an array of structs to return
            foreach($childcats as $index => $category) {
                $elements[$index]['description'] = $category['name'];
                $elements[$index]['htmlUrl'] = 'http://xartest.hsdev.com';
                $elements[$index]['rssUrl'] = 'http://xartest.hsdev.com';
            }
        }
    }
    
    
    //xarLogMessage(print_r($elements,true), XARLOG_LEVEL_WARNING);
    // Simple debugging can be done by assigning a value to $err at this point
    if (!empty($err)) {
        $out = xarModAPIFunc('xmlrpcserver','user','faultresponse',array('errorstring' => $err));
    }    else {
        // Return value of the method is an array
        $data['categories'] = $elements;
        $out = xarModAPIFunc('xmlrpcserver','user','createresponse',
                                array('module'  => 'metaweblogapi',
                                      'command' => 'getcategories',
                                      'params'  => $data)
                                );
    }
    return $out;
}
?>
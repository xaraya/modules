<?php

/**
 * File: $Id$
 *
 * Get blogs of a user
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage  bloggerapi
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

/**
 * Return categories from Xaraya
 * 
 * Takes an xmlrpc enveloped message according to blogger api and
 * uses it to return categories which are the Xaraya equivalent of blogs.
 *
 * @param  xmlrpcmsg   xml-rpc message with the parameters defined in blogger API
 * @return xmlrpcresp  Returns an xmlrpc response message, which contains the 
 *                     list of topics on success or errormessage on failure
 * @see    xmlrpc_userapi_call(), xmlrpcresp, xmlrpcmsg
 * @todo   should we only return categories to which user has access rights?
 */
function bloggerapi_userapi_getusersblogs($msg) {
    xarLogMessage("blogger api: getUsersBlogs");
	// get the params, we skip appkey for now..
	$sn1=$msg->getParam(1);  $username = $sn1->scalarval();
	$sn2=$msg->getParam(2);  $password = $sn2->scalarval();
	
    // Try to login
    if (!xarUserLogin($username, $password)) {
        $err = xarML('Invalid user (#(1)) while getting users blogs',$username);
    } else {
        if (xarModIsHooked('categories','articles')) {
            // Logged in, load categories in which articles can be published
            // Get the publication type configured for blogging
            $pubtype=xarModGetVar('bloggerapi','bloggerpubtype');
            if ($pubtype!=0) {
                // Get the root categories for this publication type
                $categories=array();
                $rootcats = xarModAPIFunc('articles','user','getrootcats',array('ptid'=>$pubtype));
                if (!empty($rootcats)) {
                    //$categories = $rootcats;
                    foreach ($rootcats as $rootcat) {  
                        // FIXME: who is responsible for security here?
                        $childcats = xarModAPIFunc('categories','user','getcat',array('return_itself'=>true,'getchildren'=>true,'cid'=>$rootcat['catid']));
                        $categories = array_merge($categories, $childcats);
                    }
                } else {
                    $err = xarML("The configured publication type has no root category!");
                }
                //xarLogVariable('categories',$categories);
                if (empty($categories)) {
                    $err = xarML("No categories available for blogging");
                }
            } else {
                $err=xarML("No publication type is registered for blogging on this site");
            }
        } else {
            $err = xarML("The articles module is not hooked up to any blog categories");
        }
    }
    
    // Simple debugging can be done by assigning a value to $err at this point
	if (!empty($err)) {
        $output = xarModAPIFunc('xmlrpcserver','user','faultresponse',array('errorstring' => $err));
	}	else {
		// otherwise, we create the right response
        $i = 0;
        $catlist=array();
        foreach ($categories as $category) {
            $url= xarModURL('articles','user','view', array('catid' => $category['cid'], 'ptid' => $pubtype));
						$catlist[$i]['url']=$url;
            $catlist[$i]['blogid']=$category['cid'];
            $catlist[$i]['blogname']=$category['name'];
			$i++;
		}
        $data['categories'] = $catlist;
        $output = xarModAPIFunc('xmlrpcserver','user','createresponse',
                                array('module'  => 'bloggerapi',
                                      'command' => 'getusersblogs',
                                      'params'  => $data)
                                );
    }
    //xarLogMessage($output);
    return $output;
}
?>
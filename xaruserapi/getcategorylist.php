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
/**
 * Get the Category list
 */
function moveabletype_userapi_getCategoryList($args)
{
    xarLogMessage('Moveabletype api: getCategoryList', XARLOG_LEVEL_WARNING);
    extract($args);

    // get the params
    $sn1=$msg->getParam(0);  $blogid        = $sn1->scalarval();
    $sn2=$msg->getParam(1);  $username      = $sn2->scalarval();
    $sn3=$msg->getParam(2);  $password      = $sn3->scalarval();

    // Try to login
    $err='';
    $elements = array();
    if (empty($password) || !xarUserLogin($username,$password)) {
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
                $elements[$index]['categoryId'] = $category['cid'];
                $elements[$index]['categoryName'] = $category['name'];
            }
        }
    }

    // Simple debugging can be done by assigning a value to $err at this point
    if (!empty($err)) {
        $out = xarModAPIFunc('xmlrpcserver','user','faultresponse',array('errorstring' => $err));
    }    else {
        // Return value of the method is an array
        $data['categories'] = $elements;
        $out = xarModAPIFunc('xmlrpcserver','user','createresponse',
                             array('module'  => 'moveabletype',
                                   'command' => 'getcategorylist',
                                   'params'  => $data)
                             );
    }
    return $out;
}
?>
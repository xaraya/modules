<?php

function moveabletype_userapi_getpostcategories($args)
{
    xarLogMessage('Moveabletype api: getpostcategories', XARLOG_LEVEL_WARNING);
    extract($args);
    
    // get the params
    $sn1=$msg->getParam(0);  $postid        = $sn1->scalarval(); // NOTE: NOT blogid
    $sn2=$msg->getParam(1);  $username      = $sn2->scalarval();
    $sn3=$msg->getParam(2);  $password      = $sn3->scalarval();

    // Try to login 
    $err='';
    $elements = array();
    if (!xarUserLogin($username,$password)) {
        $err = xarML("Invalid username or password for (#(1)) while getting recent posts",$username);
    } else {
        $pubtype=xarModGetVar('bloggerapi','bloggerpubtype');
        // Get the categories for the item.
        if(xarModIsAvailable('categories')) {
            $itemCats = xarModAPIFunc('categories','user','getitemcats',array('modid' => 151, 'itemtype' => $pubtype, 'itemid' => $postid));
        } else {
            $err = xarML("The categories module is not available, this is required");
        }
        $itemCatKeys = array_keys($itemCats);
        // First get the root categories of the publication type (as in: all the blogs)
        $rootCats = array();
        $rootCats = xarModGetVar('articles','mastercids.'.$pubtype);
        if (!empty($rootCats)) $rootCats = explode(';',$rootCats);

        $blog = array_intersect($rootCats, $itemCatKeys);
        $blog = $blog[0];
        
        if(!empty($itemCats)) {
            // Construct an array of structs to return
            foreach($itemCats as $index => $category) {
                if($index == $blog) continue;
                $elements[$index]['isPrimary']  = 0;
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
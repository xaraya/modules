<?php

/**
 * Get info about a blogging user
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bloggerapi
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

/**
 * Retrieve info about a user
 * 
 * Takes an xmlrpc enveloped message according to blogger api and
 * uses it to return user info from Xaraya
 *
 * @param  xmlrpcmsg   xml-rpc message with the parameters defined in blogger API
 * @return xmlrpcresp  Returns an xmlrpc response message, which contains the 
 *                     user info on success or errormessage on failure
 * @see    xmlrpc_userapi_call(), xmlrpcresp, xmlrpcmsg
 */
function bloggerapi_userapi_getuserinfo($args) 
{
    extract($args);
    xarLogMessage("blogger api: getUserInfo");
    // get the params, we skip appkey for now..
    $sn1=$msg->getParam(1); $username= $sn1->scalarval();
    $sn2=$msg->getParam(2); $password= $sn2->scalarval();
    
    $err='';
    if (empty($password) || !xarUserLogin($username,$password)) {
        $err = xarML("Invalid user #(1) while getting user info",$username);
    } else {
        // Get the user info
        $uid = xarUserGetVar('uid');
        $userinfo = xarModAPIFunc('roles','user','get',array('uid'=>$uid));
        if (!$userinfo) {
            $err=xarML("No user info found for #(1)",$username);
        }
    }
    
    if (!empty($err)) {
        $output = xarModAPIFunc('xmlrpcserver','user','faultresponse',array('errorstring' => $err));
    }    else {
        // otherwise, we create the right response in a struct
        $data['nickname'] = $userinfo['uname'];
        $data['userid'] = $userinfo['uid'];
        $data['email'] = $userinfo['email'];
        $data['lastname'] = $userinfo['name'];
        $data['url'] = xarModURL('roles', 'user', 'display',  array('uid' => $uid));
        $data['firstname'] = '';
        
        $output = xarModAPIFunc('xmlrpcserver','user','createresponse',
                                array('module'  => 'bloggerapi',
                                      'command' => 'getuserinfo',
                                      'params'  => $data)
                                );
    }
    return $output;
    
}
?>
<?php
/**
* File: $Id$
 *
 * Return the dispatch mapping for the metaweblog api
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage metaweblogapi
 * @author Marcel van der Boom <marcel@xaraya.com>
 */


function metaweblogapi_userapi_getdmap() 
{
    // Data types for xmlrpc
    $dataTypes = xarModAPIFunc('xmlrpcserver','user','getdatatypes');
    extract($dataTypes);
    
    // metaWeblog.getCategories(string blogid, string username, string password) : 
    $getCategories_sig = array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString,$xmlrpcString));
    $getCategories_doc = "
        Return a list of categories which can be linked to a posting.
        Parameters: blogid, username, password
        The method returns a an array of structs with description, htmlUrl and rssUrl";
    
    // metaWeblog.getRecentPosts(string blogid, string username, string password) : array
    $getRecentPosts_sig = array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcI4));
    $getRecentPosts_doc = "
        Get the most recent posts from the specified blog. The fourth parameter denotes
        the number of posts to get. If that number is larger than the number of 
        available posts, this method returns all posts";
    
    // metaWeblog.newPost(blogid, username, password, struct, publish) : string
    $newPost_sig = array(array($xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcStruct, $xmlrpcBoolean));
    $newPost_doc = "
        Creates a new posting in the weblog";
    
    // metaWeblog.editPost(postid, username, password, struct, publish) : boolean
    $editPost_sig = array(array($xmlrpcBoolean, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcStruct, $xmlrpcBoolean));
    $editPost_doc = "
        Edits and modifies an existing entry in a weblog";
    
    // metaWeblog.getPost(postid, username, password) :struct
    $getPost_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString));
    $getPost_doc = "
        Retrieve a post based on the id passed in as the first parameter";
    
    //metaWeblog.newMediaObject(string blogid, string username, string password, struct file) : url
    $newMedia_sig = array(array($xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcStruct));
    $newMedia_doc = "
        Sends a base64 encoded media object with a certain mimetype to the server. The parameters should at
        least include 'name', 'type' and 'bits'.";
    
    // Construct the dmap
    $metaweblog_dmap=array("metaWeblog.getCategories" => array("function" => "metaweblogapi_userapi_getcategories",
                                                               "signature" => $getCategories_sig,
                                                               "docstring" => $getCategories_doc),
                           "metaWeblog.getRecentPosts" => array("function" => "metaweblogapi_userapi_getrecentposts",
                                                                "signature"=> $getRecentPosts_sig,
                                                                "docstring"=> $getRecentPosts_doc),
                           "metaWeblog.newPost"        => array("function" => "metaweblogapi_userapi_newpost",
                                                                "signature" => $newPost_sig,
                                                                "docstring" => $newPost_doc),
                           "metaWeblog.editPost"       => array("function" => "metaweblogapi_userapi_editpost",
                                                                "signature" => $editPost_sig,
                                                                "docstring" => $editPost_doc),
                           "metaWeblog.getPost"        => array("function" => "metaweblogapi_userapi_getpost",
                                                                "signature" => $getPost_sig,
                                                                "docstring" => $getPost_doc),
                           "metaWeblog.newMediaObject" => array("function" => "metaweblogapi_userapi_newmediaobject",
                                                                "signature" => $newMedia_sig,
                                                                "docstring" => $newMedia_doc)
                           );
    return $metaweblog_dmap;
    
}

   
?>
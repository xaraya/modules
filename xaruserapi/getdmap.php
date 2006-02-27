<?php
/**
 * Return the dispatch mapping for the moveabletype api
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage moveabletype
 * @author Marcel van der Boom <marcel@xaraya.com>
 */
/**
 * Return the dispatch mapping for the moveabletype api
 * @return array
 */
function moveabletype_userapi_getdmap()
{
    // Data types for xmlrpc
    $dataTypes = xarModAPIFunc('xmlrpcserver','user','getdatatypes');
    extract($dataTypes);

    // mt.getRecentPostTitles
    $getRecentPostTitles_sig = array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString,$xmlrpcI4));
    $getRecentPostTitles_doc="
        Description: Returns a bandwidth-friendly list of the most recent posts in the system.

        Parameters: String blogid, String username, String password, int numberOfPosts

        Return value: on success, array of structs containing ISO.8601 dateCreated,
            String userid, String postid, String title; on failure, fault

        Notes: dateCreated is in the timezone of the weblog blogid";

    // mt.getCategoryList
    $getCategoryList_sig = array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString));
    $getCategoryList_doc = "
        Description: Returns a list of all categories defined in the weblog.

        Parameters: String blogid, String username, String password

        Return value: on success, an array of structs containing String categoryId and String categoryName; on failure, fault.";

    // mt.getPostCategories
    $getPostCategories_sig = array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString));
    $getPostCategories_doc ="
        Description: Returns a list of all categories to which the post is assigned.

        Parameters: String postid, String username, String password

        Return value: on success, an array of structs containing String categoryName, String categoryId, and boolean isPrimary; on failure, fault.

        Notes: isPrimary denotes whether a category is the post's primary category.";

    // mt.setPostCategories
    $setPostCategories_sig = array(array($xmlrpcBoolean, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcArray));
    $setPostCategories_doc = "
        Description: Sets the categories for a post.

        Parameters: String postid, String username, String password, array categories

        Return value: on success, boolean true value; on failure, fault

        Notes: the array categories is an array of structs containing String categoryId and boolean isPrimary. Using isPrimary to set the primary category is optional--in the absence of this flag, the first struct in the array will be assigned the primary category for the post.";

    // mt.supportedMethods
    $supportedMethods_sig = array(array($xmlrpcArray));
    $supportedMethods_doc = "
        Description: Retrieve information about the XML-RPC methods supported by the server.

        Parameters: none

        Return value: an array of method names supported by the server.";

    // mt.supportedTextFilters
    $supportedTextFilters_sig = array(array($xmlrpcArray));
    $supportedTextFilters_doc = "
        Description: Retrieve information about the text formatting plugins supported by the server.

        Parameters: none

        Return value: an array of structs containing String key and String label. key is the unique string identifying a text formatting plugin, and label is the readable description to be displayed to a user. key is the value that should be passed in the mt_convert_breaks parameter to newPost and editPost.";

    // mt.getTrackbackPings
    $getTrackbackPings_sig = array(array($xmlrpcArray, $xmlrpcString));
    $getTrackbackPings_doc = "
        Description: Retrieve the list of TrackBack pings posted to a particular entry. This could be used to programmatically retrieve the list of pings for a particular entry, then iterate through each of those pings doing the same, until one has built up a graph of the web of entries referencing one another on a particular topic.

        Parameters: String postid

        Return value: an array of structs containing String pingTitle (the title of the entry sent in the ping), String pingURL (the URL of the entry), and String pingIP (the IP address of the host that sent the ping).";

    // mt.publishPost
    $publishPost_sig = array(array($xmlrpcBoolean, $xmlrpcString, $xmlrpcString, $xmlrpcString));
    $publishPost_doc = "
        Description: Publish (rebuild) all of the static files related to an entry from your weblog. Equivalent to saving an entry in the system (but without the ping).

        Parameters: String postid, String username, String password

        Return value: on success, boolean true value; on failure, fault";

    // Construct the dmap
    $moveabletype_dmap=array("mt.getRecentPostTitles" => array("function" => "moveabletype_userapi_getRecentPostTitles",
                                                               "signature" => $getRecentPostTitles_sig,
                                                               "docstring" => $getRecentPostTitles_doc),
                            "mt.getCategoryList"      => array("function" => "moveabletype_userapi_getCategoryList",
                                                               "signature"=> $getCategoryList_sig,
                                                               "docstring"=> $getCategoryList_doc),
                            "mt.getPostCategories"    => array("function" => "moveabletype_userapi_getPostCategories",
                                                               "signature" => $getPostCategories_sig,
                                                               "docstring" => $getPostCategories_doc),
                            "mt.setPostCategories"    => array("function" => "moveabletype_userapi_setPostCategories",
                                                               "signature" => $setPostCategories_sig,
                                                               "docstring" => $setPostCategories_doc),
                            "mt.supportedMethods"     => array("function" => "moveabletype_userapi_supportedMethods",
                                                               "signature" => $supportedMethods_sig,
                                                               "docstring" => $supportedMethods_doc),
                            "mt.supportedTextFilters" => array("function" => "moveabletype_userapi_supportedTextFilters",
                                                               "signature" => $supportedTextFilters_sig,
                                                               "docstring" => $supportedTextFilters_doc),
                            "mt.getTrackbackPings"    => array("function" => "moveabletype_userapi_getTrackbackPings",
                                                               "signature" => $getTrackbackPings_sig,
                                                               "docstring" => $getTrackbackPings_doc),
                            "mt.publishPost"          => array("function" => "moveabletype_userapi_publishPost",
                                                               "signature" => $publishPost_sig,
                                                               "docstring" => $publishPost_doc)
                           );
    return $moveabletype_dmap;

}


?>
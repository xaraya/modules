<?php

/**
 * File: $Id$
 *
 * Return the dispatch mapping for the blogger api
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bloggerapi
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function bloggerapi_userapi_getdmap() 
{
    // Data types for xmlrpc
    $xmlrpcI4="i4";
    $xmlrpcInt="int";
    $xmlrpcBoolean="boolean";
    $xmlrpcDouble="double";
    $xmlrpcString="string";
    $xmlrpcDateTime="dateTime.iso8601";
    $xmlrpcBase64="base64";
    $xmlrpcArray="array";
    $xmlrpcStruct="struct";
    
    /**
     * appkey :(string): Unique identifier/passcode of the application sending the post.
     * username (string): Login for a user who has permission to post to the blog.
     * password (string): Password for said username.
     */
    $getUserInfo_sig=array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString));
    $getUserInfo_doc=
        "Returns a struct containing users userid, firstname, lastname,
nickname, email, and url.

Example request:
http://plant.blogger.com/api/samples/getUserInfoRequest.xml

Examples response:
http://plant.blogger.com/api/samples/getUserInfoResponse.xml";
    
    /**
     * appkey (string): Unique identifier/passcode of the application sending the post.
     * username (string): Login for a user who has permission to post to the blog.
     * password (string): Password for said username.
     */
    $getUsersBlogs_sig=array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString));
    $getUsersBlogs_doc=
        "Returns information about all the blogs a
given user is a member of. Data is returned as an array of <struct>s containing
the ID (blogid), name (blogName), and URL (url) of each blog";
    
    /**
     * appkey (string): Unique identifier/passcode of the application sending the post.
     * blogid (string): Unique identifier of the blog the posts will be fetched from.
     * username (string): Login for a user who has permission to post to the blog.
     * password (string): Password for said username.
     * numberOfPosts (int): The number of posts to fetch.
     */
    // NOTE:
    // - mozblog sends i4
    // - w.blogger sends int
    // So, we register both signatures
    $getRecentPosts_sig=array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcInt),
                              array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcI4));
    $getRecentPosts_doc=
        "Gets the n most recent stories from a Xaraya weblog. Returns
an array of structs containing the latest n posts to a given
blog, newest first.

Each post struct includes: dateCreated (when post was made), userid
(who made the post), postid, and content.

A request would look something like this:
http://plant.blogger.com/api/samples/getRecentPostsRequest.xml

A successful response would look kinds like this:
http://plant.blogger.com/api/samples/getRecentPostsResponse.xml
Notes:

* numberOfPosts is limited to 20 at this time. Let me know if this
gets annoying. Letting this number get too high could result in some
expensive db access, so I want to be careful with it.

* dateCreated is returned in the timezone of the blog.

* user, of course, must be a member of the blog";
    
    /**
     * appkey (string): Unique identifier/passcode of the application sending the post.
     * postid (string): Unique identifier of the post to be changed.
     * username (string): Login for a user who has permission to post to the blog.
     * password (string): Password for said username.
     * content (string): Contents of the post.
     * publish (boolean): If true, the blog will be published immediately after the post is made.
     */
    $editPost_sig=array(array($xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcBoolean));
    $editPost_doc=
        "Edits a story on a Xaraya weblog.
Optionally, will publish the blog the post belongs
to after changing the post. On success, it returns a boolean true value.
On error, it will return a fault with an error message";
    
    /**
     *  NOTE The first parameter of the signature is the return value
     *  appkey (string): Unique identifier/passcode of the application sending the post.
     *  blogid (string): Unique identifier of the blog the post will be added to.
     *   username (string): Login for a user who has permission to post to the blog.
     *   password (string): Password for said username.
     *   content (string): Contents of the post.
     *   publish (boolean): If true, the blog will be published immediately after the post is made.
     */
    $newPost_sig=array(array($xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcBoolean));
    $newPost_doc=
        "Posts a story to a Xaraya weblog.
Optionally, will publish the blog after making the post.
On success, it returns the unique ID of the new post.
On error, it will return some error message.";
    
    /** 
     *    appkey (string): Unique identifier/passcode of the application sending the post.
     *    postid (string): Unique identifier of the post that will be fetched.
     *    username (string): Login for a user who has permission to post to the blog.
     *    password (string): Password for said username.
     */
    $getPost_sig=array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString));
    $getPost_doc=
"Gets the specified story on a Xaraya weblog. Returns
a struct (like the structs in getRecentPosts) containing the
userid, post body, datecreated, and post id. There may be additional
fields returned in the future.
  
Example request:
http://plant.blogger.com/api/samples/getPostRequest.xml
 
Example response:
http://plant.blogger.com/api/samples/getPostResponse.xml

User must be a member of the blog. (They do not need to be the one
who made the post or an admin -- as any member of a blog can see
other members posts, though they cannot necessarily edit them.)

And yes, I will get these last couple methods added to documentation
soon.

Note: If you are doing something similar to the blogger.com interface,
where you show a user the most recent posts and they click on one to
edit and then it loads in a form, it would, of course, be better to
cache the data from getRecentPosts() than to subsequently call this.
But this will be handy if you know the post ID and are not calling
getRecentPosts first. For example, it might written out on a
published page.";
    
    /**
     * the params are:
     *    appkey (string): Unique identifier/passcode of the application sending the post.
     *    postid (string): Unique identifier of the post to be changed.
     *    username (string): Login for a Blogger user who has permission to post to the blog.
     *    password (string): Password for said username.
     *    publish (boolean): If true, the blog will be published immediately after the post is made.
     */
    $deletePost_sig=array(array($xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcBoolean));
    $deletePost_doc='Deletes a story on a Xaraya weblog. Returns .';
    
    // Construct the dmap
    $blogger_dmap=array("blogger.newPost"        => array("function" => "bloggerapi_userapi_newpost",
                                                          "signature" => $newPost_sig,
                                                          "docstring" => $newPost_doc),

                        "blogger.editPost"       => array("function" => "bloggerapi_userapi_editpost",
                                                          "signature" => $editPost_sig,
                                                          "docstring" => $editPost_doc),
                        
                        "blogger.deletePost"     => array("function" => "bloggerapi_userapi_deletepost",
                                                          "signature" => $deletePost_sig,
                                                          "docstring" => $deletePost_doc),
                        
                        "blogger.getPost"        =>	 array("function" => "bloggerapi_userapi_getpost",
                                                           "signature" => $getPost_sig,
                                                           "docstring" => $getPost_doc),
                        
                        "blogger.getRecentPosts" =>	array("function" => "bloggerapi_userapi_getrecentposts",
                                                          "signature" => $getRecentPosts_sig,
                                                          "docstring" => $getRecentPosts_doc),
                        

                        "blogger.getUserInfo"    =>	array("function" => "bloggerapi_userapi_getuserinfo",
                                                          "signature" => $getUserInfo_sig,
                                                          "docstring" => $getUserInfo_doc),

                       
                        "blogger.getUsersBlogs"  =>	array("function" => "bloggerapi_userapi_getusersblogs",
                                                          "signature" => $getUsersBlogs_sig,
                                                           "docstring" => $getUsersBlogs_doc)
                        );
    return $blogger_dmap;

}
?>
<?php

/**
* $Id$
 *
 * Implementation of the metaWeblog.getRecentPosts method
 *
 * The signatures of blogger.RecentPosts and metaWeblog.recentPosts are
 * the same, let the blogger api handle this
 *
 */

function metaweblogapi_userapi_getrecentposts($args)
{
    xarLogMessage("MetaWeblog api: getRecentPosts");
    return xarModAPIFunc('bloggerapi','user','getrecentposts',$args);
}
?>
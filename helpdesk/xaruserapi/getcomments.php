<?php
//=========================================================================
// Get ticket comments
//=========================================================================
function helpdesk_userapi_getcomments($args)
{
    extract($args);

    $modid = xarModGetIDFromName(xarModGetName());

    $comments = xarModAPIFunc('comments', 'user', 'get_multiple', 
                              array('modid' => $modid,
                                    'objectid' => $tid));

    if (is_array($comments) && count($comments) > 0) {
        if (!xarModAPILoad('comments', 'renderer')) {
            return false;
        }
        comments_renderer_array_markdepths_bychildren($comments);
        comments_renderer_array_sort($comments, _COM_SORTBY_DATE, _COM_SORT_DESC);

    }
    return $comments;
}
?>

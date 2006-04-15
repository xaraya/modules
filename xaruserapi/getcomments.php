<?php
/**
 * Helpdesk Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Helpdesk Module
 * @link http://www.abraisontechnoloy.com/
 * @author Brian McGilligan <brianmcgilligan@gmail.com>
 */
/*
    Get ticket comments
*/
function helpdesk_userapi_getcomments($args)
{
    extract($args);

    if( empty($itemid) ){ return false; }

    $modid = xarModGetIDFromName(xarModGetName());

    $comments = xarModAPIFunc('comments', 'user', 'get_multiple',
        array(
            'modid' => $modid,
            'objectid' => $itemid
        )
    );

    if (is_array($comments) && count($comments) > 0)
    {
        if( !xarModAPILoad('comments', 'renderer') ){ return false; }
        comments_renderer_array_markdepths_bychildren($comments);
        comments_renderer_array_sort($comments, _COM_SORTBY_DATE, _COM_SORT_ASC);
    }
    return $comments;
}
?>
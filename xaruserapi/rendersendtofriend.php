<?php
/** 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Send To A Friend
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
*/
/*
 * Handle Send to a friend tag
 *
 * ProvHandling for <xar:recommend-sendtofriend ...>  tags
 * Format : <xar:recommend-sendtofriend /> or
 *          Typical usage is <xar:recommend-sendtofriend type="text" /> will display the link
 *          or <xar:recommend-sendtofriend type="icon" /> will display the mail icon.
 *           or <xar:recommend-sendtofriend type="text"  text="sometext" /> will display the text provided
 *          The icon is displayed by default when no attributes provided.
 *          Placed in template  where you wish the 'send to friend' icon or text link to appear
 *
 * @author jojodee
 * @param $args containing option for $type  - either "link" or "icon"
 * @returns string
 * @return the PHP code needed to invoke user_sendtofriend() in the BL template
 */
function recommend_userapi_rendersendtofriend($args)
{
    extract ($args);

    if(!xarVarFetch('aid',  'id',   $aid,   NULL, XARVAR_NOT_REQUIRED)) {
        $aid= xarVarGetCached('Blocks.articles','aid');
    }
    if (!isset($aid)) {
        $out='';
        return $out;
    }
    if (empty($type)) {$type = 'icon';}

    if (isset($text) && !empty($text)) {
        $linktext = $text;
    } else {
        $linktext=xarML("Send to a friend");
    }
    $alttext = xarML('Send this article to a friend');

    $link=xarModURL('recommend','user','sendtofriend',array('aid'=>'$aid'));

    $sendimg = xarTplGetImage('sendtofriend.gif', 'recommend');
    if ($type=='text') {
        $out= 'echo "<a href=\"'.$link.'\">'.$linktext.'</a>"';
    } else {
        $out= 'echo "<a href=\"'.$link.'\"><img src=\"'.$sendimg.'\" style=\"border:0;\" alt=\"'.$alttext.'\" /></a>"';
    }
return $out;
}

?>

<?php
/** 
 * File: $Id$
 *
 * Handle Send to a friend tag
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Send To A Friend
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
*/
/**
 * Handle <xar:recommend-sendtofriend ...>  tags
 * Format : <xar:recommend-sendtofriend /> or
 * <xar:recommend-sentofriend type="link|icon|somethingelse??" />
 * Typical usage is <xar:recommend-sendtofriend type="text" /> will display the link
 * or <xar:recommend-sendtofriend type="icon" /> will display the mail icon.
 * The icon is displayed by default when no attributes provided.
 * Placed in template  where you wish the 'send to friend' icon or text link to appear
 * @author jojodee
 * @param $args containing option for $type  - either "link" or "icon"
 * @returns string
 * @return the PHP code needed to invoke user_sendtofriend() in the BL template
 */
function recommend_userapi_rendersendtofriend($args)
{
    extract ($args);

    if (empty($type)) {$type = '';}

    //TODO: <jojodee> make easier with text configurable, along with text for email itself.
    $linktext=xarML("Send to a friend");
    $alttext = xarML('Send this article to a friend');

    $sendimg = xarTplGetImage('sendtofriend.gif', 'recommend');
    if ($type=='text') {
        $out= 'echo "<a href=\"'.xarModURL('recommend','user','sendtofriend',array('aid'=>'$aid')).'\">'.$linktext.'</a>"';
    } else {
        $out= 'echo "<a href=\"'.xarModURL('recommend','user','sendtofriend',array('aid'=>'$aid')).'\"><img src=\"'.$sendimg.'\" style=\"border:0;\" alt=\"'.$alttext.'\" /></a>"';
    }
return $out;
}


?>

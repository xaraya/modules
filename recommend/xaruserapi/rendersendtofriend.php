<?php

/**
 * Handle <xar:recommend-sendtofriend ...>  tags
 * Format : <xar:recommend-sendtofriend />
 * Typical usage is <xar:recommend-sendtofriend /> 
 * placed where you wish the 'send to friend' icon to appear
 * @author jojodee
 * @param At this stage does not take attributes
 * @returns string
 * @return the PHP code needed to invoke user_sendtofriend() in the BL template
 */
function recommend_userapi_rendersendtofriend($args)
{
    extract ($args);
//   if(!xarVarFetch('aid', 'isset', $aid, NULL, XARVAR_DONT_SET)) {return;}


        $alttext = xarML('Send this article to a friend');
        $sendimg = xarTplGetImage('sendtofriend.gif', 'recommend');
        $out= 'echo "<a href=\"'.xarModURL('recommend','user','sendtofriend',array('aid'=>'$aid')).'\"><img src=\"'.$sendimg.'\" style=\"border:0;\" alt=\"'.$alttext.'\" /></a>"';

return $out;
}


?>

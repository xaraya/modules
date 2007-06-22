<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Recommend Module
 * @author John Cox
*/
/**
 * @ Function: user_sendtofriend called with <xar:recommend-sendtofriend /> custom tag
 * @ also used by dyn data property SendToFriend (which may not remain but allows show or not show per item)
 * @ Author jojodee
 * @ Parameters $aid used to determine $title and $ptid for display URL construction
 */
function recommend_user_sendtofriend($args)
{
    if(!xarVarFetch('aid', 'isset', $aid,   NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('message', 'str:1:', $message, '', XARVAR_NOT_REQUIRED)) return;
    // Security Check
    if(!xarSecurityCheck('OverviewRecommend')) return;
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Thank You')));
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    $data['submit'] = xarML('Submit');
    $articleinfo=xarModAPIFunc('articles','user','get',array('aid'=>$aid));
    $data['title']=$articleinfo['title'];
    $ptid=$articleinfo['pubtypeid'];
    $articledisplay=xarModURL('articles','user','display',array('aid'=>$aid,'ptid'=>$ptid));
    $data['htmldisplaylink']='<a href="'.$articledisplay.'" >'.$articledisplay.'</a>';
    $data['textdisplaylink']=$articledisplay;
    $data['aid']=$aid;

    if ($message == 1){
        $data['message'] = xarML('Thank You For Recommending An Article!');
        $data['message'] .='<br />';
        $data['message'] .= xarML('An email has been sent to your friend.');
    } else {
        $data['message'] = '';
    }

    return $data;
}
?>
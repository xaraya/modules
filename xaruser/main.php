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
/* Main user function for Recommend
 * @author John Cox
 */
function recommend_user_main($args)
{
    extract($args);
    if (!xarVarFetch('message', 'str:1:', $message, '', XARVAR_NOT_REQUIRED)) return;
    if(!xarSecurityCheck('ReadRecommend')) return;
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Thank You')));
    /* Generate a one-time authorisation code for this operation */
    $data['authid'] = xarSecGenAuthKey();
    $data['submit'] = xarML('Submit');
    if ($message == 1){
        $data['message'] = xarML('Thank You For Recommending Us!');
    } else {
        $data['message'] = '';
    }
    return $data;
}
?>
<?php
/**
 * File: $Id: s.xarinit.php 1.11 03/01/18 11:39:31-05:00 John.Cox@mcnabb. $
 * 
 * Xaraya Recommend
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Recommend Module
 * @author John Cox
*/

function recommend_user_main($args)
{
    extract($args);
    if (!xarVarFetch('message', 'str:1:', $message, '', XARVAR_NOT_REQUIRED)) return;
    if(!xarSecurityCheck('OverviewRecommend')) return;
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Thank You')));
    // Generate a one-time authorisation code for this operation
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
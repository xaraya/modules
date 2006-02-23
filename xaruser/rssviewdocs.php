<?php
/**
 * Display a release
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @author Release module development team
 */
/**
 * Display a release
 *
 * @param rid ID
 * 
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 */
function release_user_rssviewdocs()
{
    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    // The user API function is called. 
    $id = xarModAPIFunc('release',
                         'user',
                         'getallids',
                          array('certified' => '2'));


}

?>
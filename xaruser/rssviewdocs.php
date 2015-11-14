<?php
/**
 * Display a release
 *
 * @package modules
 * @subpackage Release Module
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @link http://xaraya.com/index.php/release/773.html
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
    $id = xarMod::apiFunc('release', 'user', 'getallids',
                          array('certified' => '2'));


}

?>
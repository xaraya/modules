<?php
/**
 * Display waiting content
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
 */
/**
 * Display waiting content
 * 
 * @param 
 * 
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 */
/**
 * display waiting content as a hook
 */
function release_admin_waitingcontent()
{
    
    // Get releasenotes
    unset($released);
    $released = xarModAPIFunc('release', 'user', 'getreleaselinks',
                          array('approved' => 1));

     $data['loop'] = $released;
     $data['counted']=$released['counted'];

     return $data;
}
?>
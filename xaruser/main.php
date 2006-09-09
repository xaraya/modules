<?php
/**
 * User main function
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
 * Main user function
 * 
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 * @return array empty
 */
function release_user_main()
{
    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;
    
    return array();

}

?>
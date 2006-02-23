<?php
/**
 * User main function
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
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
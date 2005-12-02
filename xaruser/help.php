<?php
/**
 * File: $Id:
 * 
 * User help function
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf 
 */
function bible_user_help()
{ 
    if (!xarSecurityCheck('ViewBible')) return;

    $data = xarModAPIFunc('bible', 'user', 'menu', array('func' => 'help')); 

    xarTplSetPageTitle(xarML('Overview')); 

    // Return the template variables defined in this function
    return $data; 
} 

?>

<?php
/*
 * File: $Id: $
 *
 * phpLDAPadmin 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team 
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage phpldapadmin module
 * @author Richard Cave <rcave@schwabfoundation.org : rcave@xaraya.com>
 * @link http://xavier.schwabfoundation.org
*/

/**
 * Run phpLDAPadmin
 *
 * @author Richard Cave
 * @returns redirect to phpLDAPadmin
 */
function phpldapadmin_admin_phpldapadmin()
{
    // Security check
    if(!xarSecurityCheck('AdminphpLDAPadmin')) return;
    
    // Set the object paremeters
    $data = array();
    $data['page'] = 'modules/phpldapadmin/phpldapadmin/index.php';
    $data['title'] = 'phpLDAPadmin';
    $data['hsize'] = '800'; // Don't user 100% as it won't display
    $data['vsize'] = '600'; // Don't user 100% as it won't display 

    return xarTplModule('phpldapadmin','admin','phpldapadmin', $data);
}

?>

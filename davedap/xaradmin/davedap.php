<?php
/*
 * File: $Id: $
 *
 * DaveDAP 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team 
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage davedap module
 * @author Richard Cave <rcave@schwabfoundation.org : rcave@xaraya.com>
 * @link http://xavier.schwabfoundation.org
*/

/**
 * Run DaveDAP
 *
 * @author Richard Cave
 * @returns redirect to DaveDAP
 */
function davedap_admin_davedap()
{
    // Security check
    if(!xarSecurityCheck('AdminDaveDAP')) return;
    
    // Set the object paremeters
    $data = array();
    $data['page'] = 'modules/davedap/davedap/index.php';
    $data['title'] = 'DaveDAP';
    $data['hsize'] = '800'; // Don't user 100% as it won't display
    $data['vsize'] = '600'; // Don't user 100% as it won't display 

    return xarTplModule('davedap','admin','davedap', $data);
}

?>

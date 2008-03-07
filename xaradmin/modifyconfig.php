<?php
/**
 * Mime module
 *
 * @package modules
 * @copyright (C) 2002-2008 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage mime
 * @link http://xaraya.com/index.php/release/999.html
 * @author Ernst Herbst 
 */
/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function mime_admin_modifyconfig()
{ 
    // Security Check
    if (!xarSecurityCheck('AdminAll')) return; 
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey(); 
    // everything else happens in Template for now
    return $data;
} 

?>
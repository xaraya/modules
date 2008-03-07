<?php
/**
 * Mime module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage mime
 * @link http://xaraya.com/index.php/release/999.html
 * @author Ernst Herbst  
 */

/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function mime_admin_updateconfig()
{ 
    // Get parameters
    if (!xarVarFetch('mimemethod', 'str:1', $mimeMethod)) return; 
    if (!xarVarFetch('mimepath', 'str:1', $mimePath, '', XARVAR_NOT_REQUIRED)) return;
    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return; 
    // Security Check
    if (!xarSecurityCheck('AdminAll')) return; 
    xarModSetVar('mime', 'mimemethod', $mimeMethod); 
    xarModSetVar('mime', 'mimepath', $mimePath);
    // lets update status and display updated configuration
    xarResponseRedirect(xarModURL('mime', 'admin', 'modifyconfig')); 
    // Return
    return true;
} 
?>
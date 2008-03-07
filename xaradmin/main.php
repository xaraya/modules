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
 * @author Ernst Herbst hb@xaraya.com 
 */
/**
 * The main administration function
 * This function redirects to the modify config function
 * @access public 
 * @return bool true on success
 */
function mime_admin_main()
{
    // Security check
    if(!xarSecurityCheck('AdminAll')) return;
    // Redirect
    xarResponseRedirect(xarModURL('mime', 'admin', 'modifyconfig'));
    return true;
}

?>
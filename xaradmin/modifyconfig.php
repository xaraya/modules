<?php
/**
 * Modify module's configuration
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage flickring
 * @author Johnny Robeson 
 */

/**
 * Modify module's configuration
 *
 * @return array
 */
function flickring_admin_modifyconfig()
{ 
    $data = array();
    
    if (!xarSecurityCheck('AdminFlickring')) return;

    return $data;
}
?>

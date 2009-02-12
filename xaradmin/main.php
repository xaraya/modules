<?php
/**
 * Access Methods Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Access Methods Module
 * @link http://xaraya.com/index.php/release/732.html
 * @author St.Ego <webmaster@ivory-tower.net>
 */
/**
 * the main administration function
 */
function accessmethods_admin_main()
{
    if (!xarSecurityCheck('EditAccessMethods')) return;

    $data = xarModAPIFunc('accessmethods','admin','menu');

    // Return the template variables defined in this function
    return $data;
}

?>

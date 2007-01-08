<?php
/**
 * Julian Main administration function
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */
/**
 * the main administration function
 *
 * This function doesn't do much but present a template
 *
 * @author MichelV <michelv@xaraya.com>
 * @return array Just show a template
 */
function julian_admin_main()
{
    if (!xarSecurityCheck('EditJulian')) return;
    return array();
}
?>
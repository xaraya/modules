<?php
/**
 * Julian Main administration function
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */
/**
 * the main administration function
 * @return mixed
 */
function julian_admin_main()
{
    if (!xarSecurityCheck('Editjulian')) return;
    return array();

}
?>
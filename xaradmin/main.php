<?php
/**
 * Main administration
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage labAccounting Module
 * @link http://xaraya.com/index.php/release/706.html
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * the main administration function
 */
function labAccounting_admin_main()
{
    if (!xarSecurityCheck('AdminAccounting')) return;

    $data = xarModAPIFunc('labAccounting','admin','menu');

    // Return the template variables defined in this function
    return $data;
}

?>
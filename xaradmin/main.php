<?php
/**
 * Main administration
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data Example Module
 * @link http://xaraya.com/index.php/release/66.html
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * the main administration function
 */
function dyn_example_admin_main()
{
    if (!xarSecurityCheck('EditDynExample')) return;

    $data = xarModAPIFunc('dyn_example','admin','menu');

    // Return the template variables defined in this function
    return $data;
}

?>
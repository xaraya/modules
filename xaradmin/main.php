<?php
/**
 * Main administration
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data Example Module
 * @link http://xaraya.com/index.php/release/66.html
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * the main administration function
 * @param none
 * @return array
 */
function dyn_example_admin_main()
{
    // Check to see the current user has edit access to the dyn_example module
    if (!xarSecurityCheck('EditDynExample')) return;

    // Return the template variables defined in this function
    // Nothing defined here, so just return an empty array
    return array();
}

?>
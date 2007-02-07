<?php
/**
 * The main user function
 *
 * @package modules
 * @copyright (C) 2006-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage JpGraph Module
 * @link http://xaraya.com/index.php/release/819.html
 * @author JpGraph Module Development Team
 */
/**
 * The main user function
 *
 * This function is the default function, and is called whenever the module is
 * initiated without defining arguments. It will show a text telling that this module
 * has no user interface, but only an admin interface.
 *
 * @author the JpGraph module development team
 * @return array $data An array with the data for the template
 */
function jpgraph_user_main()
{
    /* Security check */
    if (!xarSecurityCheck('ViewJpGraph')) return;

    $data = array();

    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('JpGraph')));
    /* Return the template variables defined in this function */
    return $data;
}
?>
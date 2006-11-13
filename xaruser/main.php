<?php
/**
 * The main user function
 *
 * @package modules
 * @copyright (C) 2006 The Digital Development Foundation
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
 * initiated without defining arguments. As such it can be used for a number
 * of things, but most commonly it either just shows the module menu and
 * returns or calls whatever the module designer feels should be the default
 * function (often this is the view() function)
 *
 * @author the JpGraph module development team
 * @return array $data An array with the data for the template
 */
function jpgraph_user_main()
{
    /* Security check - important to do this as early as possible to avoid
     * potential security holes or just too much wasted processing. For the
     * main function we want to check that the user has at least overview
     * privilege for some item within this component, or else they won't be
     * able to see anything and so we refuse access altogether. The lowest
     * level of access for users depends on the particular module, but it is
     * generally either 'overview' or 'read'
     */
    if (!xarSecurityCheck('ViewJpGraph')) return;
    /* If you want to go directly to some default function, instead of
     * having a separate main function, you can simply call it here, and
     * use the same template for user-main.xard as for user-view.xard
     * return xarModFunc('jpgraph','user','view');
     * Initialise the $data variable that will hold the data to be used in
     * the blocklayout template, and get the common menu configuration - it
     * helps if all of the module pages have a standard menu at the top to
     * support easy navigation
     */
    $data = array();

    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('JpGraph')));
    /* Return the template variables defined in this function */
    return $data;
}
?>
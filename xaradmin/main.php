<?php
/**
 * The main administration function
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Surveys Module
 * @link http://xaraya.com/index.php/release/45.html
 */

/**
 * The main administration function
 *
 * This function is the default function, and is called whenever the
 * module is initiated without defining arguments.  As such it can
 * be used for a number of things, but most commonly it either just
 * shows the module menu and returns or calls whatever the module
 * designer feels should be the default function (often this is the
 * view() function)
 *
 * @author Surveys Module Development Team
 */
function surveys_admin_main()
{
    /* Security check */
    if (!xarSecurityCheck('EditSurvey')) return;
    /* redirect to most important function
     */
    xarResponseRedirect(xarModURL('surveys', 'admin', 'viewusersurveys'));
    /* success so return true */
    return true;
}
?>
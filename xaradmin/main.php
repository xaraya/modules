<?php
/**
 * Site Tools Main Admin
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sitetools Module
 * @link http://xaraya.com/index.php/release/887.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * The main administration function
 * This function is the default function, and is called whenever the
 * module is initiated without defining arguments.
 */
function sitetools_admin_main()
{ 
    /* Security check */
    if (!xarSecurityCheck('EditSiteTools')) return;

        $data = xarModAPIFunc('sitetools', 'admin', 'menu');
        $data['welcome'] = '';
  
        xarResponseRedirect(xarModURL('sitetools', 'admin', 'modifyconfig'));

    return true;
}
?>
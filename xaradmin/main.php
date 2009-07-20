<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 */
 /**
 * The main administration function
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @access public
 * @return true on redirect
 */
function crispbb_admin_main()
{
    // since the user entered here, we try and determine where to redirect them to
    // no need for sec checks, the function we redirect to can take care of it

    // try for current users last view
    $defaultfunc = xarSessionGetVar('crispbb_adminstartpage');

    // fall back to module config
    if (empty($defaultfunc)) {
        $defaultfunc = xarModGetVar('crispbb', 'adminstartpage');
    }

    // menulinks returns a keyed array of valid functions
    $menulinks = xarModAPIFunc('crispbb', 'admin', 'getmenulinks');
    // if the default func is empty or isn't valid we fall back to overview
    if (empty($defaultfunc) || !isset($menulinks[$defaultfunc])) {
        $defaultfunc = 'overview';
    }

    xarResponseRedirect(xarModURL('crispbb', 'admin', $defaultfunc));
    return true;
}
?>
<?php

/**
 * File: $Id$
 *
 * Main admin page
 *
 * @package Xaraya
 * @copyright (C) 2004 by Jason Judge
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.academe.co.uk/
 * @author Jason Judge
 * @subpackage xarpages
 */

function xarpages_admin_main()
{
    // Need admin priv to view the info page.
    if (!xarSecurity::check('AdminXarpagesPage')) {
        return;
    }

    // Redirect to the view page.
    xarController::redirect(xarController::URL('xarpages', 'admin', 'viewpages'));
    return true;
}

<?php
/**
 * Gallery
 *
 * @package   Xaraya eXtensible Management System
 * @copyright (C) 2006 by Brian McGilligan
 * @license   New BSD License <http://www.abrasiontechnology.com/index.php/page/7>
 * @link      http://www.abrasiontechnology.com/
 *
 * @subpackage Gallery module
 * @author     Brian McGilligan
 */

/**
    Admin Overview
*/
function gallery_admin_main()
{
    if( !Security::check(SECURITY_ADMIN, 'gallery') ){ return false; }

    xarResponseRedirect(xarModURL('gallery', 'admin', 'view'));

    return false;
}
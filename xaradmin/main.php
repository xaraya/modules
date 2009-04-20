<?php
/**
 * Entry point for translations admin
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/


/**
 * Entry point for translations admin screen
 *
 * A somewhat longer description of the function which may be 
 * multiple lines, can contain examples.
 *
 * @access  public
 * @return  array template data
*/
function translations_admin_main()
{
    xarResponse::Redirect(xarModURL('translations', 'admin', 'start'));
    return array();
}

?>
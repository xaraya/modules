<?php
/**
 * Translations bulk operations
 *
 * @package modules
 * @copyright (C) 2003-2009 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/


/**
 * Entry point for translations bulk operations
 *
 * @access  public
 * @return  array template data
*/
function translations_admin_bulk()
{
    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

?>
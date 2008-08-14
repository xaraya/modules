<?php
/**
 * File: $Id
 *
 * MIME table definitions function
 *
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage mime
 * @author Carl P. Corliss <carl.corliss.com>
 */

/**
 * Upgraded to the new security schema by Vassilis Stratigakis
 * http://www.tequilastarrise.net
 */

/**
 * Return mime table names to xaraya
 *
 * This function is called internally by the core whenever the module is
 * loaded.  It is loaded by xarMod__loadDbInfo().
 *
 * @access private
 * @return array
 */
function mime_xartables()
{
    // Initialise table array
    $xartable = array();

    $mime_type      = xarDB::getPrefix() . '_mime_type';
    $mime_subtype   = xarDB::getPrefix() . '_mime_subtype';
    $mime_extension = xarDB::getPrefix() . '_mime_extension';
    $mime_magic     = xarDB::getPrefix() . '_mime_magic';

    // Set the table name
    $xartable['mime_type']      = $mime_type;
    $xartable['mime_subtype']   = $mime_subtype;
    $xartable['mime_extension'] = $mime_extension;
    $xartable['mime_magic']     = $mime_magic;

    // Return the table information
    return $xartable;
}

?>
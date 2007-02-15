<?php
/**
/**
 * Mime Module table definitions
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage mime
 * @author Carl P. Corliss
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

    $mime_type      = xarDBGetSiteTablePrefix() . '_mime_type';
    $mime_subtype   = xarDBGetSiteTablePrefix() . '_mime_subtype';
    $mime_extension = xarDBGetSiteTablePrefix() . '_mime_extension';
    $mime_magic     = xarDBGetSiteTablePrefix() . '_mime_magic';

    // Set the table name
    $xartable['mime_type']      = $mime_type;
    $xartable['mime_subtype']   = $mime_subtype;
    $xartable['mime_extension'] = $mime_extension;
    $xartable['mime_magic']     = $mime_magic;

    // Return the table information
    return $xartable;
}

?>
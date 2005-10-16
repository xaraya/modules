<?php
/**
 * xarLinkMe table definition functions
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarlinkme
 * @link http://xaraya.com/index.php/release/889.html
 * @author jojodee <jojodee@xaraya.com>
 */
/**
 * xarLinkMetable definition functions
 * Return table names to xaraya
 *
 * @access private
 * @return array
 */
function xarlinkme_xartables()
{ 
    /* Initialise table array */
    $xarTables = array();
    /* Get the name for the example item table.  This is not necessary
     * but helps in the following statements and keeps them readable
     */

    $xlmbannerstable  = xarDBGetSiteTablePrefix() . '_xlm_banners';
    $xlmbannersexpiredtable = xarDBGetSiteTablePrefix() . '_xlm_banners_expired';
    /* Set the table name */
    $xarTables['xlm_banners'] = $xlmbannerstable;
    $xarTables['xlm_banners_expired'] = $xlmbannersexpiredtable;

    /* Return the table information */
    return $xarTables;
}
?>
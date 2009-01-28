<?php
/**
 * Table definition functions
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */
/**
 * Table definition functions
 *
 * Return Example module table names to xaraya
 *
 * This function is called internally by the core whenever the module is
 * loaded. It is loaded by xarMod__loadDbInfo().
 * @author Example Module development team
 * @access private
 * @return array
 */
function twitter_xartables()
{
    /* Initialise table array */
    $xarTables = array();
    /* Get the name for the example item table. This is not necessary
     * but helps in the following statements and keeps them readable
     */
    // $twitterTable = xarDBGetSiteTablePrefix() . '_twitter';

    /* Set the table name */
    // $xarTables['twitter'] = $twitterTable;
    /* Return the table information */
    return $xarTables;
}
?>
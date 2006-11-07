<?php
/**
 * Table definition functions
 *
 * @package modules
 * @copyright (C) 2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage PHPlot Module
 * @link http://xaraya.com/index.php/release/818.html
 * @author PHPlot Module Development Team
 */
/**
 * Table definition functions
 *
 * Return PHPlot module table names to xaraya
 *
 * This function is called internally by the core whenever the module is
 * loaded. It is loaded by xarMod__loadDbInfo().
 * @author PHPlot Module development team
 * @access private
 * @return array
 */
function phplot_xartables()
{
    /* Initialise the empty table array */
    $xarTables = array();

    /* Return the table information */
    return $xarTables;
}
?>
<?php
/**
 * Scheduler Module
 *
 * @package modules
 * @subpackage scheduler module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/189.html
 * @author mikespub
 */
/**
 * Return scheduler table names to Xaraya (none at the moment)
 *
 * This function is called internally by the core whenever the module is
 * loaded.  It is loaded by xarMod::loadDbInfo().
 *
 * @access private
 * @return array
 */
function scheduler_xartables()
{
    $tables = array();
    $tables['scheduler_jobs'] = xarDB::getPrefix() . '_scheduler_jobs';
    return $tables;
}

<?php
/**
 * Dynamic Data Example Module  Table Creation
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data Example Module
 * @link http://xaraya.com/index.php/release/732.html
 * @author mikespub <mikespub@xaraya.com>
 */

/**
 * This function is called internally by the core whenever the module is
 * loaded.  It adds in the information
 */
function accessmethods_xartables()
{
    $xartable = array();
    $accessmethods_table = xarDBGetSiteTablePrefix() . '_accessmethods';
    $xartable['accessmethods'] = $accessmethods_table;
    return $xartable;
}

?>

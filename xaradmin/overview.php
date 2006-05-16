<?php
/**
* Display module overview
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage files
* @link http://xaraya.com/index.php/release/554.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
 * Display module overview
 * @return array
 */
function files_admin_overview()
{
    if (!xarSecurityCheck('AdminFiles', 0)) return;

    $data = array();
    return $data;
}

?>
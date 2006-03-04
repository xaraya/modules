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
    // security check
    if (!xarSecurityCheck('AdminFiles', 0)) return;

    // initialize template data
    $data = xarModAPIFunc('files', 'admin', 'menu');

    // return output of main admin function (the overview page)
    return xarTplModule('files', 'admin', 'main', $data, 'main');
}

?>
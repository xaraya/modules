<?php
/**
* Display module overview
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage bible
* @link http://xaraya.com/index.php/release/550.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* Display module overview
*/
function bible_admin_overview()
{
    // security check
    if (!xarSecurityCheck('AdminBible', 0)) return;

    // initialize template data
    $data = xarModAPIFunc('bible', 'admin', 'menu');

    // show overview page
    return xarTplModule('bible', 'admin', 'main', $data, 'main');
}

?>
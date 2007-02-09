<?php
/**
* main admin function
*
* @package unassigned
* @copyright (C) 2002-2007 The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage highlight
* @link http://xaraya.com/index.php/release/559.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* main administration function
*/
function highlight_admin_main()
{
    // security check
    if (!xarSecurityCheck('AdminHighlight')) return;

    // Initialise array
    $data = xarModAPIFunc('highlight', 'admin', 'menu');

    // success
    return $data;
}

?>

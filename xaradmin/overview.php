<?php
/**
* Display module overview
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* Display module overview
*/
function ebulletin_admin_overview()
{
    if (!xarSecurityCheck('AdmineBulletin', 0)) return;

    $data = array();
    return $data;
}

?>
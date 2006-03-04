<?php
/**
 * Search main administration function
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005-2006 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Search
 * @author Jo Dalle Nogare
 */
/**
 * the Search Overview function
 */
function search_admin_overview()
{
    if (!xarSecurityCheck('AdminSearch')) return;
    $data=array();
    // success
    return $data;
}

?>

<?php
/**
 * Display a list of recent entries
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

include_once 'modules/encyclopedia/xarclasses/encyclopediaquery.php';

function encyclopedia_adminapi_getrecent()
{
    if (!xarSecurityCheck('ReadEncyclopedia')) {return;}

    $q = new EncyclopediaQuery();
    $q->eq('active',1);
    if(!$q->run()) return;
    return $q->output();
}

?>
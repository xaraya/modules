<?php
/**
 * Display the inactive terms in the encyclopedia
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

function encyclopedia_adminapi_getinactive($args)
{
    if (!xarSecurityCheck('ReadEncyclopedia')) {return;}

    $q = new EncyclopediaQuery();
    $q->eq('active',0);
    if(!$q->run()) return;
    return $q->output();
}

?>
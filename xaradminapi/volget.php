<?php
/**
 * Get a specific volume
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

function encyclopedia_adminapi_volget($args)
{
    extract($args);
    if (!isset($vid)) return;

    $item = xarModAPIFunc('categories','user','getcatinfo', array('cid' => $vid));
    $volume = $item['name'];
    $description = $item['description'];

    if (!xarSecurityCheck('ReadEncyclopedia',0,'Volume',$volume . "::" . $vid)) {return;}

    $volume = array('vid' => $vid,
                  'volume' => $volume,
                  'description' => $description);

    return $volume;
}

?>
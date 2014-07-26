<?php
/**
 * Calendar Module
 *
 * @package modules
 * @subpackage calendar module
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2014 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

sys::import('xaraya.structures.query');

function calendar_userapi_getevents($args)
{
    extract($args);
    $xartable =& xarDB::getTables();

    $q = new Query('SELECT');
    $q->addtable($xartable['calendar_event']);
    $q->ge('start_time',$day->thisDay(TRUE));
    $q->lt('start_time',$day->nextDay(TRUE));

    if (!$q->run()) return;
    return $q->output();
}

?>

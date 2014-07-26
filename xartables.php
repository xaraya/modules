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

function calendar_xartables()
{
    $xartables = array();
    $prefix = xarDB::getPrefix() . '_calendar';

    $xartables['calendar_calendar'] = $prefix . '_calendar';
    $xartables['calendar_event'] = $prefix . '_event';

    return $xartables;
} ?>

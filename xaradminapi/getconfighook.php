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

function calendar_adminapi_getconfighook($args)
{
    extract($args);
    if (!isset($extrainfo['tabs'])) $extrainfo['tabs'] = array();
    $module = 'quotas';
    $tabinfo = array(
            'module'  => $module,
            'configarea'  => 'general',
            'configtitle'  => xarML('Calendar'),
            'configcontent' => ''
    );
    $extrainfo['tabs'][] = $tabinfo;
    return $extrainfo;
}
?>
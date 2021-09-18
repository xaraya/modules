<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
function publications_adminapi_getconfighook($args)
{
    extract($args);
    if (!isset($extrainfo['tabs'])) {
        $extrainfo['tabs'] = [];
    }
    $module = 'publications';
    $tabinfo = [
            'module'  => $module,
            'configarea'  => 'general',
            'configtitle'  => xarML('Publications'),
            'configcontent' => xarMod::guiFunc(
                $module,
                'admin',
                'modifyconfig_general'
            ),
    ];
    $extrainfo['tabs'][] = $tabinfo;
    return $extrainfo;
}

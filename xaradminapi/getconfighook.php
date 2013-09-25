<?php
/**
 * EAV Module
 *
 * @package modules
 * @subpackage eav
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2013 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Handle getconfig hook calls
 *
 */

    function eav_adminapi_getconfighook($args)
    {
        extract($args);
        if (!isset($extrainfo['tabs'])) $extrainfo['tabs'] = array();
        $module = 'eav';
        $tabinfo = array(
                'module'  => $module,
                'configarea'  => 'general',
                'configtitle'  => xarML('EAV'),
                'configcontent' => ''
        );
        $extrainfo['tabs'][] = $tabinfo;
        return $extrainfo;
    }
?>
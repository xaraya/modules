<?php
/**
 * Foo Module
 *
 * @package modules
 * @subpackage foo module
 * @copyright (C) 2011 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Handle getconfig hook calls
 *
 */

    function foo_adminapi_getconfighook($args)
    {
        extract($args);
        if (!isset($extrainfo['tabs'])) $extrainfo['tabs'] = array();
        $module = 'foo';
        $tabinfo = array(
                'module'  => $module,
                'configarea'  => 'general',
                'configtitle'  => xarML('Foo'),
                'configcontent' => ''
        );
        $extrainfo['tabs'][] = $tabinfo;
        return $extrainfo;
    }
?>
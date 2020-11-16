<?php
/**
 * Payments Module
 *
 * @package modules
 * @subpackage payments
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2016 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Handle getconfig hook calls
 *
 */

    function payments_adminapi_getconfighook($args)
    {
        extract($args);
        if (!isset($extrainfo['tabs'])) {
            $extrainfo['tabs'] = array();
        }
        $module = 'payments';
        $tabinfo = array(
                'module'  => $module,
                'configarea'  => 'general',
                'configtitle'  => xarML('Payments'),
                'configcontent' => ''
        );
        $extrainfo['tabs'][] = $tabinfo;
        return $extrainfo;
    }

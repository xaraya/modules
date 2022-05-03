<?php
/**
 * Logconfig initialization functions
 *
 * @package modules
 * @subpackage logconfig
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2022 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * A central registry of all the variables our loggers use
 * 
 * The keys are the logger object properties, the values are the corresponding fields in the configuration file
 */
function logconfig_adminapi_get_variables()
{
    $vars = [
    		'filename'    => 'Filename',
    		'maxfilesize' => 'MaxFileSize',
    		'loglevel'    => 'Level',
    		'mode'        => 'Mode',
    		'recipient'   => 'Recipient',
    		'sender'      => 'Sender',
    		'subject'     => 'Subject',
    		'timeformat'  => 'Timeformat',
    		'sqltable'    => 'SQLTable',
    		'facility'    => 'Facility',
    		'options'     => 'Options',
    		'sqltable'    => 'SQLTable',
    		];

    return $vars;
}

?>
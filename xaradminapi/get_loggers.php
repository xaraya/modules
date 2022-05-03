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
 * A central registry of all our loggers
 */
function logconfig_adminapi_get_loggers()
{
    $loggers = array(
    	['id' => 'html',        'name' => 'HTML',           'object' => 'logconfig_html'],
    	['id' => 'javascript',  'name' => 'Javascript Log', 'object' => 'logconfig_javascript'],
    	['id' => 'mail',        'name' => 'Mail',           'object' => 'logconfig_mail'],
    	['id' => 'mozilla',     'name' => 'Mozilla',        'object' => 'logconfig_mozilla'],
    	['id' => 'simple',      'name' => 'Simple',         'object' => 'logconfig_simple'],
    	['id' => 'sql',         'name' => 'SQL',            'object' => 'logconfig_sql'],
    	['id' => 'syslog',      'name' => 'System Log',     'object' => 'logconfig_syslog'],
    );

    return $loggers;
}

?>
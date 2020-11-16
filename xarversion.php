<?php
/**
 * Otp Module
 *
 * @package modules
 * @subpackage otp
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2017 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 *
 * Version information
 *
 */
    $modversion['name']           = 'otp';
    $modversion['id']             = '30053';
    $modversion['version']        = '1.0.0';
    $modversion['displayname']    = xarML('Otp');
    $modversion['description']    = xarML('A generic Web 2.0 module');
    $modversion['credits']        = 'credits.txt';
    $modversion['help']           = 'help.txt';
    $modversion['changelog']      = 'changelog.txt';
    $modversion['license']        = 'license.txt';
    $modversion['official']       = false;
    $modversion['author']         = 'Marc Lutolf';
    $modversion['contact']        = 'http://www.netspan.ch/';
    $modversion['admin']          = true;
    $modversion['user']           = false;
    $modversion['class']          = 'Authentication';
    $modversion['category']       = 'Users & Groups';
    $modversion['securityschema'] = array();
    $modversion['dependency']     = array();
    $modversion['dependencyinfo'] = array(
                                    0 => array(
                                            'name' => 'Xaraya Core',
                                            'version_ge' => '2.2.0'
                                         ),
                                          );

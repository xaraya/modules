<?php
/**
 * Cacher Module
 *
 * @package modules
 * @subpackage cacher
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2014 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 *
 * Version information
 *
 */
    $modversion['name']           = 'cacher';
    $modversion['id']             = '30224';
    $modversion['version']        = '1.0.0';
    $modversion['displayname']    = xarML('Cacher');
    $modversion['description']    = xarML('A generic Web 2.0 module');
    $modversion['credits']        = 'credits.txt';
    $modversion['help']           = 'help.txt';
    $modversion['changelog']      = 'changelog.txt';
    $modversion['license']        = 'license.txt';
    $modversion['official']       = false;
    $modversion['author']         = 'Marc Lutolf';
    $modversion['contact']        = 'http://www.netspan.ch/';
    $modversion['admin']          = true;
    $modversion['user']           = true;
    $modversion['class']          = 'Complete';
    $modversion['category']       = 'Content';
    $modversion['securityschema'] = array();
    $modversion['dependency']     = array();
    $modversion['dependencyinfo'] = array(
                                    0 => array(
                                            'name' => 'Xaraya Core',
                                            'version_ge' => '2.4.0'
                                         ),
                                          );
?>
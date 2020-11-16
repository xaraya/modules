<?php
/**
 * Realms Module
 *
 * @package modules
 * @subpackage realms module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

/**
 * Version information
 */
    $modversion['name']           = 'realms';
    $modversion['id']             = '30081';
    $modversion['version']        = '1.0.0';
    $modversion['displayname']    = xarML('Realms');
    $modversion['description']    = xarML('A module for managing multi-realm sites');
    $modversion['credits']        = 'credits.txt';
    $modversion['help']           = 'help.txt';
    $modversion['changelog']      = 'changelog.txt';
    $modversion['license']        = 'license.txt';
    $modversion['official']       = false;
    $modversion['author']         = 'Marc Lutolf';
    $modversion['contact']        = 'http://www.netspan.ch/';
    $modversion['admin']          = true;
    $modversion['user']           = false;
    $modversion['class']          = 'Complete';
    $modversion['category']       = 'Users & Groups';
    $modversion['securityschema'] = array();
//    $modversion['dependency'] = array(30012);
//    $modversion['dependency'] = array(30012, 30046, 30057, 30205);
    $modversion['dependencyinfo'] = array(
                                        0 => array(
                                                'name' => 'core',
                                                'version_ge' => '2.2.0'
                                             ),
//                                          30012 => 'math',
//                                          30057 => 'authentication',
//                                          30205 => 'registration',
                                          );

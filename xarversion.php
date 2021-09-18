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
 *
 * Version information
 *
 */
    $modversion['name']           = 'eav';
    $modversion['id']             = '30091';
    $modversion['version']        = '1.0.0';
    $modversion['displayname']    = xarML('EAV');
    $modversion['description']    = xarML('A module that implements EAV');
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
    $modversion['securityschema'] = [];
    $modversion['dependency']     = [];
    $modversion['dependencyinfo'] = [
                                    0 => [
                                            'name' => 'Xaraya Core',
                                            'version_ge' => '2.2.0',
                                         ],
                                          ];

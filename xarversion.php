<?php

/**
 * Xarpages version information.
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003-2010 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarpages
 * @author Jason Judge
 */

$modversion['name']           = 'xarpages';
$modversion['id']             = '160';
$modversion['version']        = '0.3.0';
$modversion['displayname']    = 'Xarpages';
$modversion['description']    = 'Static pages administration';
$modversion['help']           = 'xardocs/privileges.txt';
$modversion['changelog']      = 'xardocs/news.txt';
$modversion['official']       = 1;
$modversion['author']         = 'Jason Judge';
$modversion['contact']        = 'http://www.academe.co.uk/';
$modversion['admin']          = 1;
$modversion['user']           = 1;
$modversion['securityschema'] = [];
$modversion['class']          = 'Complete';
$modversion['category']       = 'Content';
$modversion['dependencyinfo']   = [
                                    0 => [
                                            'name' => 'core',
                                            'version_ge' => '1.2.0-b1',
                                         ],
                                    147 => [
                                            'name' => 'categories',
                                            'version_ge' => '2.4.0',
                                        ],
                                ];

if (false) {
    xarML('Xarpages');
    xarML('Static pages administration');
}

<?php

/**
 * Xarpages version information.
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003-2009 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarpages
 * @author Jason Judge
 */

$modversion['name']           = 'xarpages';
$modversion['id']             = '160';
$modversion['version']        = '2.0.1';
$modversion['displayname']    = 'Xarpages';
$modversion['description']    = 'Static pages administration';
$modversion['help']           = 'xardocs/privileges.txt';
$modversion['changelog']      = 'xardocs/news.txt';
$modversion['official']       = true;
$modversion['author']         = 'Jason Judge';
$modversion['contact']        = 'http://www.academe.co.uk/';
$modversion['admin']          = true;
$modversion['user']           = true;
$modversion['securityschema'] = array();
$modversion['class']          = 'Complete';
$modversion['category']       = 'Content';
// 147 = 'categories';
$modversion['dependency']     = array(147);
$modversion['dependencyinfo'] = array(
                                    0 => array(
                                            'name' => 'Xaraya Core',
                                            'version_ge' => '2.1.0',
                                            'version_le' => '2.1.99',
                                         ),
                                    147 => 'categories',
                                      );
if (false) {
    xarML('Xarpages');
    xarML('Static pages administration');
}
?>
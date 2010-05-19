<?php
/**
 * uspsws
 *
 * @package modules
 * @copyright (C) 2009 WebCommunicate.net
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage uspsws
 * @link http://xaraya.com/index.php/release/1033.html
 * @author Ryan Walker <ryan@webcommunicate.net>
 **/
$modversion['name']         = 'uspsws';
$modversion['id']           = '1033';
$modversion['version']      = '0.5.0';
$modversion['displayname']  = xarML('USPS Web Services');
$modversion['description']  = 'USPS rate calculator';
$modversion['credits']      = 'xardocs/credits.txt';
$modversion['help']         = 'xardocs/help.txt';
$modversion['changelog']    = 'xardocs/changelog.txt';
$modversion['license']      = 'xardocs/license.txt';
$modversion['official']     = false;
$modversion['author']       = 'potion';
$modversion['contact']      = 'http://www.webcommunicate.net/';
$modversion['admin']        = true;
$modversion['user']         = true;
$modversion['class']        = 'Complete';
$modversion['category']     = 'Commerce';
$modversion['dependencyinfo'] = array(
                                    0 => array(
                                            'name' => 'Xaraya Core',
                                            'version_ge' => '2.1.0'
                                         ),
                                      );
?>

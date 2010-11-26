<?php
/**
 * Twitter Module
 *
 * @package modules
 * @copyright (C) 2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Twitter Module
 * @link http://xaraya.com/index.php/release/991.html
 * @author Chris Powis (crisp@crispcreations.co.uk)
 */
$modversion['name']           = 'twitter';
$modversion['id']             = '991';
$modversion['version']        = '0.9.0';
$modversion['displayname']    = 'Twitter';
$modversion['description']    = 'Twitter module';
$modversion['changelog']      = 'xardocs/changelog.txt';
$modversion['official']       = 1;
$modversion['author']         = 'Chris Powis';
$modversion['contact']        = 'http://crispcreations.co.uk/';
$modversion['admin']          = 1;
$modversion['user']           = 0;
$modversion['class']          = 'Complete';
$modversion['category']       = 'Content';
$modversion['dependency']     = array(189201,189206); // depends on LibOAuth, LibTwitterOAuth modules 
$modversion['dependencyinfo'] = array(
                                    0 => array(
                                            'name' => 'Xaraya Core',
                                            'version_ge' => '2.2.0',
                                            //'version_le' => '2.1.99',
                                         ),
                                    189201 => array('name' => 'liboauth'),
                                    189206 => array('name' => 'libtwitteroauth'),
                                      );
?>
<?php
/**
 * Headlines - Generates a list of feeds
 *
 * @package modules
 * @copyright (C) 2005-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @link http://www.xaraya.com/index.php/release/777.html
 * @author John Cox
 */
$modversion['name']         = 'Headlines';
$modversion['id']           = '777';
$modversion['version']      = '2.0.0';
$modversion['displayname']  = xarML('Headlines');
$modversion['description']  = 'Generates a list of feeds.';
$modversion['official']     = 1;
$modversion['author']       = 'John Cox';
$modversion['contact']      = 'niceguyeddie@xaraya.com';
$modversion['admin']        = 1;
$modversion['user']         = 1;
$modversion['class']        = 'Complete';
$modversion['category']     = 'Content';
$modversion['dependency'] = array(
//                                  151
                                  );
$modversion['dependencyinfo'] = array(
                                    0 => array(
                                            'name' => 'Xaraya Core',
                                            'version_ge' => '2.1.0'
                                         ),
                                      151  => 'articles',
                                      );
?>
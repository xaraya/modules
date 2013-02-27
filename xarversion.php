<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
$modversion['name']         = 'publications';
$modversion['id']           = '30065';
$modversion['version']      = '2.0.0';
$modversion['displayname']  = xarML('Publications');
$modversion['description']  = xarML('Manage publications on a Xaraya site');
$modversion['credits']      = '';
$modversion['help']         = '';
$modversion['changelog']    = '';
$modversion['license']      = '';
$modversion['official']     = 1;
$modversion['author']       = 'M. Lutolf';
$modversion['contact']      = 'http://www.netspan.ch/';
$modversion['admin']        = true;
$modversion['user']         = true;
$modversion['class']        = 'Complete';
$modversion['category']     = 'Content';
$modversion['dependency']   = array(147);
$modversion['dependencyinfo'] = array(
                                    0 => array(
                                            'name' => 'Xaraya Core',
                                            'version_ge' => '2.2.0'
                                         ),
                                    30066 => array(
                                            'name' => 'ckeditor',
                                            'minversion' => '1.0.0'
                                         ),
                                      );
$modversion['propertyinfo'] = array(
                                    30039 => array(
                                        'name' => 'language',
                                        ),
                                    30059 => array(
                                        'name' => 'datetime',
                                        ),
                                    );
?>

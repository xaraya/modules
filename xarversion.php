<?php
/**
 * Figlet Module
 *
 * @package modules
 * @subpackage figlet module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Lucas Baltes, John Cox
 */
    $modversion['name'] = 'Figlet';
    $modversion['id'] = '740';
    $modversion['version'] = '2.0.0';
    $modversion['displayname']    = xarML('Figlet');
    $modversion['description'] = 'Transform text to ascii art';
    $modversion['credits'] = 'xardocs/faq.txt';
    $modversion['help'] = 'xardocs/faq.txt';
    $modversion['official'] = 1;
    $modversion['author'] = 'Lucas Baltes, John Cox';
    $modversion['contact'] = 'lucas@thebobo.com, admin@dinerminor.com';
    $modversion['admin'] = 1;
    $modversion['user'] = 1;
    $modversion['securityschema'] = array('figlet::' => '::');
    $modversion['class'] = 'Utility';
    $modversion['category'] = 'Global';
    $modversion['dependencyinfo'] = array(
                                    0 => array(
                                            'name' => 'Xaraya Core',
                                            'version_ge' => '2.3.0',
                                         ),
                                      );
?>
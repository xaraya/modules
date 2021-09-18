<?php
/**
 * Scraper Module
 *
 * @package modules
 * @subpackage scraper
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2019 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 *
 * Version information
 *
 */
    $modversion['name']           = 'scraper';
    $modversion['id']             = '30228';
    $modversion['version']        = '1.0.0';
    $modversion['displayname']    = xarML('Scraper');
    $modversion['description']    = xarML('A module for scraping HTML pages');
    $modversion['credits']        = 'credits.txt';
    $modversion['help']           = 'help.txt';
    $modversion['changelog']      = 'changelog.txt';
    $modversion['license']        = 'license.txt';
    $modversion['official']       = false;
    $modversion['author']         = 'Marc Lutolf';
    $modversion['contact']        = 'http://www.luetolf-carroll.com/';
    $modversion['admin']          = true;
    $modversion['user']           = true;
    $modversion['class']          = 'Complete';
    $modversion['category']       = 'Content';
    $modversion['securityschema'] = [];
    $modversion['dependency']     = [];
    $modversion['dependencyinfo'] = [
                                    0 => [
                                            'name' => 'Xaraya Core',
                                            'version_ge' => '2.4.0',
                                         ],
                                          ];

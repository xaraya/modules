<?php
/**
 * Site Manager Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Site Manager Module
 * @link http://xaraya.com/index.php/release/66.html
 * @author St.Ego <webmaster@ivory-tower.net>
 */

/**
 * This function is called internally by the core whenever the module is
 * loaded.  It adds in the information
 */
function xproject_xartables()
{
    $xartable = array();
    $xProjects = xarDBGetSiteTablePrefix() . '_xProjects';
    $xartable['xProjects'] = $xProjects;
    $xProject_features = xarDBGetSiteTablePrefix() . '_xProject_features';
    $xartable['xProject_features'] = $xProject_features;
    $xProject_pages = xarDBGetSiteTablePrefix() . '_xProject_pages';
    $xartable['xProject_pages'] = $xProject_pages;
    $xProject_log = xarDBGetSiteTablePrefix() . '_xProject_log';
    $xartable['xProject_log'] = $xProject_log;
    return $xartable;
}
?>
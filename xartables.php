<?php
/**
 * Photoshare by Chris van de Steeg
 * based on Jorn Lind-Nielsen 's photoshare
 * module for PostNuke
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage photoshare
 * @author Chris van de Steeg
 */

/**
 * This function is called internally by the core whenever the module is
 * loaded.  It adds in information about the tables that the module uses.
 */
function photoshare_xartables()
{
  $xartable = array();
  $xartable['photoshare_folders'] = xarDBGetSiteTablePrefix() . '_photoshare_folders';
  $xartable['photoshare_images'] = xarDBGetSiteTablePrefix() . '_photoshare_images';
  $xartable['photoshare_setup'] = xarDBGetSiteTablePrefix() . '_photoshare_setup';
  
  return $xartable;
}

?>

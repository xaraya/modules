<?php
/**
 * Photoshare by Jorn Lind-Nielsen (C) 2002.
 *
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage photoshare
 * @author Jorn Lind-Nielsen / Chris van de Steeg
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
<?php 
/**
 * File: $Id$
 * 
 * Xaraya Site Cloud
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Site Cloud Module
 * @author John Cox
*/

/**
 *
 * @author  John Cox 
 * @access  public
 * @param   none
 * @return  $xartable array
 * @throws  no exceptions
 * @todo    nothing
*/
function sitecloud_xartables()
{
    // Initialise table array
    $xartable = array();
    $sitecloud = xarDBGetSiteTablePrefix() . '_sitecloud';
    $xartable['sitecloud'] = $sitecloud;
    return $xartable;
}
?>
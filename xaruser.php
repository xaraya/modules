<?php
/**
 * File: $Id$
 *
 * Used to load the xaruserapi file which contains module defaults
 *
 * @package unassigned
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage calendar
 * @link http://xaraya.simiansynapse.com
 * @author Roger Raymond <roger@xaraya.com>
 */

//======================================================================
//  Load the User API
//  This allows us to load Module defaults without loading
//  any actual API functions for each page called in this module
//======================================================================
xarModAPILoad('calendar','user');
?>
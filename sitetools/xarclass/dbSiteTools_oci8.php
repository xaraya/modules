<?php
/*
 * File: $Id:
 *
 * Site Tools database class
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteTools module
 * @author jojodee <jojodee@xaraya.com>
*/

/**
 * SiteTools Database abstraction class extension 
 *
 * @author Richard Cave <rcave@xaraya.com>
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 * @access private
 */
require_once('modules/sitetools/xarclass/dbSiteTools.php');

class dbSiteTools_oci8 extends dbSiteTools
{
    function _optimize()
    {
        $rowinfo = array();

        // Do something

        return $rowinfo; 
    }

    function _backup()
    {
        return true;
    }
}

?>

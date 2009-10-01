<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sitetools
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * SiteTools Database abstraction class extension 
 *
 * @author Richard Cave <rcave@xaraya.com>
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 * @access private
 */
sys::import('modules.sitetools.xarclass.dbSiteTools');

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
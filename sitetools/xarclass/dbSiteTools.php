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
 * Database abstraction class for SiteTools
 *
 * @author Richard Cave <rcave@xaraya.com>
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 * @access private
 */
class dbSiteTools
{
    // initialize some vars
    var $_database_info;
    var $dbconn;
    
    function dbSiteTools ($dbname='',$dbtype='')
    {
        if (empty($this->dbconn)) {
            $this->dbconn =& xarDBGetConn();
        }
        if (empty($this->dbtype)) {
            $this->dbtype = xarDBGetType();
        } else {
            $this->dbtype =$dbtype;
        }
        if (empty($this->dbname)) {
            $this->dbname = xarDBGetName();
        } else {
            $this->dbname=$dbname;
        }
       $this->_database_info =array($this->dbtype,$this->dbconn,$this->dbname);
    }

    function optimize()
    {
        $rowinfo = $this->_optimize();

         // Return items
        return $rowinfo;
    }

    function backup()
    {
        $result = $this->_backup();
        
        // Return 
        return $result;
    }

}

?>

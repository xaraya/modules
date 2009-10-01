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
            $this->dbconn = xarDB::getConn();
        }
        if (empty($this->dbtype)) {
            $this->dbtype = xarDB::getType();
        } else {
            $this->dbtype =$dbtype;
        }
        if (empty($this->dbname)) {
            $this->dbname = xarDB::getName();
        } else {
            $this->dbname=$dbname;
        }
       $this->_database_info =array($this->dbtype,$this->dbconn,$this->dbname);
    }

    function selecttables($dbname='')
    {
        $SelectedTables = $this->_selecttables($this->dbname);
         // Return items
        return $SelectedTables;
    }

    function checktables($SelectedTables)
    {
        $TableErrors = $this->_checktables($SelectedTables);

         // Return items
        return $TableErrors;
    }
    function bkcountoverallrows($SelectedTables,$number_of_cols='')
    {
        $overallrows = $this->_bkcountoverallrows($SelectedTables,$number_of_cols);

        return $overallrows;
    }

    function backup($bkvars)
    {
        $runningstatus = $this->_backup($bkvars);
        if (!$runningstatus) {return false;}

        // Return
        return $runningstatus;
    }



}

?>
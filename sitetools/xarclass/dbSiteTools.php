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
 * @access private
 */
class dbSiteTools
{
    // initialize some vars
    var $_database_info = array(
        'type'      => '',
        'dbconn'    => array(),
        'dbname'    => '',
        );

    function init ()
    {
        if (empty($this->type)) {
            $this->type = xarDBGetType();
        }
        if (empty($this->dbname)) {
            $this->dbname = xarDBGetName();
        }
        if (empty($this->dbconn)) {
            list($this->dbconn) = xarDBGetConn();
        }
    }

    function optimize()
    {
        $rowinfo = $this->_optimize();

        $items['rowinfo']=$rowinfo['rowdata'];
        $items['total_gain']=$rowinfo['total_gain'];
        $items['total_kbs']=$rowinfo['total_kbs'];
        $items['dbname']=$this->dbname;
                                                                                           
        // Return items
        return $items;
    }

    function backup()
    {
        $result = $this->_backup();
        
        // Return 
        return $result;
    }
}

?>

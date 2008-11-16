<?php
/**
 * File: $Id$
 *
 * Soapserver init function. Xaraya 1.1.5 seems to need this.
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage jobs
 * @author Jason Judge
 */
function soapserver_init()
{

    // Initialisation successful
    return true;
}
function soapserver_upgrade($oldversion)
{
    switch($oldversion){
       case '0.0.1':
         
        break;

    }
    return true;
}
/**
 * delete the smiley module
 */
function soapserver_delete()
{

    // Deletion successful
    return true;
}
?>

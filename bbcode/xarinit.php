<?php
/**
 * File: $Id$
 * 
 * Xaraya BBCode
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage BBCode
 * @author larseneo, Hinrich Donner
*/
xarDBLoadTableMaintenanceAPI();

function bbcode_init() 
{
    // Set up database tables
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    
    // Set up module variables
    xarModSetVar('bbcode', 'dolinebreak', 0);
    xarModSetVar('bbcode', 'transformtype', 1);

    $table = $xartable['bbcode'];
    $fields = array('xar_id'            => array('type' => 'integer', 
                                                 'null' => false, 
                                                 'increment' => true, 
                                                 'primary_key' => true),
                    'xar_tag'           => array('type' => 'varchar', 
                                                 'size' => 100, 
                                                 'null' => false, 
                                                 'default' => ''),
                    'xar_name'          => array('type' => 'varchar', 
                                                 'size' => 200, 
                                                 'null' => false, 
                                                 'default' => ''),
                    'xar_description'   => array('type' => 'text'),
                    'xar_transformed'   => array('type' => 'text'));

    $query = xarDBCreateTable($table, $fields);
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    $index = array('name' => 'i_'.xarDBGetSiteTablePrefix().'_bbcode_1',
        'fields' => array('xar_tag'),
        'unique' => true);
    $query = xarDBCreateIndex($table, $index);
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    $codes = array("'[u]','underline','[u]Your Text[/u] will produce', '[u]Your Text[/u]'",
                   "'[b]','bold','[b]Your Text[/b] will produce', '[b]Your Text[/b]'",
                   "'[o]','overline','[o]Your Text[/o] will produce', '[o]Your Text[/o]'",
                   "'[lt]','linethrough','[lt]Your Text[/lt] will produce', '[lt]Your Text[/lt]'",
                   "'[sc]','smallcaps','[sc]Your Text[/sc] will produce', '[sc]Your Text[/sc]'",
                   "'[i]','italics','[i]Your Text[/i] will produce', '[i]Your Text[/i]'",
                   "'[sub]','sub','[sub]Your Text[/sub] will produce', '[sub]Your Text[/sub]'",
                   "'[sup]','sup','[sup]Your Text[/sup] will produce', '[sup]Your Text[/sup]'",
                   "'[you]','you','[you] will produce', 'The viewers name, likeso: [you]'");

    foreach ($codes as $code) {
        // Get next ID in table
        $nextId = $dbconn->GenId($table);
        $query = "INSERT INTO $table VALUES ($nextId,$code)";
        $result =& $dbconn->Execute($query);
        if (!$result) return;
    }

    xarRegisterMask('EditBBCode','All','bbcode','All','All','ACCESS_EDIT');
    xarRegisterMask('OverviewBBCode','All','bbcode','All','All','ACCESS_OVERVIEW');

    // Set up module hooks
    if (!xarModRegisterHook('item',
                           'transform',
                           'API',
                           'bbcode',
                           'user',
                           'transform')) {
        $msg = xarML('Could not register hook');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
        
    }
    if (!xarModRegisterHook('item',
                           'formheader',
                           'GUI',
                           'bbcode',
                           'user',
                           'formheader')) {
        $msg = xarML('Could not register hook');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
        
    }
    if (!xarModRegisterHook('item',
                           'formaction',
                           'GUI',
                           'bbcode',
                           'user',
                           'formaction')) {
        $msg = xarML('Could not register hook');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    if (!xarModRegisterHook('item',
                           'formdisplay',
                           'GUI',
                           'bbcode',
                           'user',
                           'formdisplay')) {
        $msg = xarML('Could not register hook');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    if (!xarModRegisterHook('item',
                           'formarea',
                           'GUI',
                           'bbcode',
                           'user',
                           'formarea')) {
        $msg = xarML('Could not register hook');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    // Initialisation successful
    return true;
}

function bbcode_upgrade($oldversion) 
{
    switch ($oldversion) {
        case '1.0':
        case '1.0.0':
            $modversion['admin']            = 1;
            xarModSetVar('bbcode', 'dolinebreak', 0);
            xarModSetVar('bbcode', 'transformtype', 1);
            xarRegisterMask('EditBBCode','All','bbcode','All','All','ACCESS_EDIT');
            // Code to upgrade from version 1.3 goes here
            // Remove module hooks
            if (!xarModUnregisterHook('item',
                                      'formfooter',
                                      'GUI',
                                      'bbcode',
                                      'user',
                                      'formfooter')) {
                $msg = xarML('Could not un-register hook');
                xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
                return;
            }
            break;

        case '1.1':
        case '1.1.0':
            $modversion['user']             = 1;
            break;
        case '1.1.1':
            $modversion['user']             = 0;
            break;
        default:
            // Couldn't find a previous version to upgrade
            return;
    }
    return true;
}

function bbcode_delete() 
{
    // Drop all ModVars
    xarModDelAllVars('bbcode');
    xarRemoveMasks('bbcode');
    xarRemoveInstances('bbcode');

    // Drop the table
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $table = $xartable['bbcode'];
    $query = xarDBDropTable($table);
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    // Remove module hooks
    if (!xarModUnregisterHook('item',
                             'transform',
                             'API',
                             'bbcode',
                             'user',
                             'transform')) {
        $msg = xarML('Could not un-register hook');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Remove module hooks
    if (!xarModUnregisterHook('item',
                           'formaction',
                           'GUI',
                           'bbcode',
                           'user',
                           'formaction')) {
        $msg = xarML('Could not un-register hook');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Remove module hooks
    if (!xarModUnregisterHook('item',
                           'formdisplay',
                           'GUI',
                           'bbcode',
                           'user',
                           'formdisplay')) {
        $msg = xarML('Could not un-register hook');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Remove module hooks
    if (!xarModUnregisterHook('item',
                           'formarea',
                           'GUI',
                           'bbcode',
                           'user',
                           'formarea')) {
        $msg = xarML('Could not un-register hook');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    // Deletion successful
    return true;
}
?>
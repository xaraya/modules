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

function bbcode_init() 
{
    // Set up module variables
    //
    xarModSetVar('bbcode', 'dolinebreak', 0);
    xarModSetVar('bbcode', 'transformtype', 1);
    xarRegisterMask('EditBBCode','All','bbcode','All','All','ACCESS_EDIT');
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
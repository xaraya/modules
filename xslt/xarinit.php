<?php
/**
 * File: $Id$
 * 
 * XSLT Transform Hook
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage XSLT Transform Hook
 * @author mikespub
 */

/**
 * Initialise the XSLT Transform Hook module
 * 
 * @author mikespub
 * @access public 
 */
function xslt_init()
{ 
    // Make sure that XSLT is available
    if (!extension_loaded('xslt')) {
        $msg=xarML('Your PHP configuration does not seem to include the required XSLT extension. Please refer to http://www.php.net/manual/en/ref.xslt.php on how to install it.');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION,'MODULE_DEPENDENCY',
                        new SystemException($msg));
        return;
    }

    // Set up module hooks
    if (!xarModRegisterHook('item', 'transform', 'API',
                            'xslt', 'user', 'transform'))
        return; 

    xarRegisterMask('AdminXSLT', 'All', 'xslt', 'All', 'All', ACCESS_ADMIN);

    xarModSetVar('xslt','default','modules/xslt/default.xsl');

    // Initialisation successful
    return true;
} 

/**
 * Upgrade the xslt transform hook module from an old version
 * 
 * @author mikespub
 * @access public 
 * @param  $oldVersion 
 * @return true on success or false on failure
 * @throws no exceptions
 */
function xslt_upgrade($oldversion)
{
    return true;
} 

/**
 * Delete the xslt transform hook module
 * 
 * @author mikespub
 * @access public 
 */
function xslt_delete()
{ 
    // Remove module hooks
    if (!xarModUnregisterHook('item', 'transform', 'API',
                              'xslt', 'user', 'transform'))
        return;

    // Remove module variables
// TODO

    // Remove Masks and Instances
    xarRemoveMasks('xslt');

    // Deletion successful
    return true;
} 

?>

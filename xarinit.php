<?php
/**
 * contains the module information
 *
 * @package modules
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage trackback
 * @author John Cox
*/
/**
 * Trackback Initialization Function
 *
 * @author John Cox
 *
 */
function trackback_init() 
{

    // display hook
    if (!xarModRegisterHook('item', 'display', 'GUI', 'trackback', 'user', 'displayhook')) return false;
    if (!xarModRegisterHook('item', 'new',     'GUI', 'trackback', 'admin', 'newhook')) return false;
    if (!xarModRegisterHook('item', 'create',  'API', 'trackback', 'admin', 'createhook')) return false;
    if (!xarModRegisterHook('item', 'modify',  'GUI', 'trackback', 'admin', 'modifyhook')) return false;
    if (!xarModRegisterHook('item', 'update',  'API', 'trackback', 'admin', 'updatehook')) return false;

    // for module access
    xarRegisterMask('Viewtrackback' ,'All' ,'trackback' ,'All' ,'All', 'ACCESS_OVERVIEW');
    xarRegisterMask('Addtrackback' ,'All' ,'trackback' ,'All' ,'All', 'ACCESS_ADD');

    // Initialisation successful
    return true;
}

function trackback_delete()
{

    if (!xarModUnregisterHook('item', 'display', 'GUI', 'trackback', 'user', 'display')) return false; 
    if (!xarModUnregisterHook('item', 'new',     'GUI', 'trackback', 'admin', 'newhook')) return false;
    if (!xarModUnregisterHook('item', 'create',  'API', 'trackback', 'admin', 'createhook')) return false;
    if (!xarModUnregisterHook('item', 'modify',  'GUI', 'trackback', 'admin', 'modifyhook')) return false;
    if (!xarModUnregisterHook('item', 'update',  'API', 'trackback', 'admin', 'updatehook')) return false;

    // Remove Masks and Instances
    xarRemoveMasks('trackback');
    xarRemoveInstances('trackback');

    // Deletion successful
    return true;
}
?>
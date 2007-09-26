<?php
// ----------------------------------------------------------------------
// Copyright (C) 2005 Marc Lutolf
// Purpose of file:  Initialisation functions for query module
// ----------------------------------------------------------------------

/**
 * initialise the system module
 */
function query_init()
{
# --------------------------------------------------------
#
# Set up masks
#
    xarRegisterMask('ViewQuery','All','query','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('AdminQuery','All','query','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Set up privileges
#
    xarRegisterPrivilege('AdminQuery','All','query','All','All','ACCESS_ADMIN');
    xarMakePrivilegeRoot('AdminQuery');

# --------------------------------------------------------
#
# Set up modvars
#
    xarModVars::set('query', 'itemsperpage', 20);
    xarModVars::set('query', 'shorturla', 0);
    xarModVars::set('query', 'useModuleAlias',0);
    xarModVars::set('query', 'aliasname','Query');
    xarModVars::set('query', 'debugmode', 0);
    xarModVars::set('query', 'debugusers', serialize(array()));
    return true;
}

function query_activate()
{
    return true;
}

function query_upgrade($oldversion)
{
    switch($oldversion){
        case '1.0.0':

    }
// Upgrade successful
    return true;
}

function query_delete()
{
    $module = 'activities';
    return xarModAPIFunc('modules','admin','standarddeinstall',array('module' => $module));
}
?>

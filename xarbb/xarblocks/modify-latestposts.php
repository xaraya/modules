<?php
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2003 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Author of file: jojodee
// Purpose of file: Update options for the xarBB Latest Post Block
// ----------------------------------------------------------------------


/**
 * modify block settings
 */
function xarbb_latestpostsblock_modify($blockinfo)
{
    // Get current content
    $vars = @unserialize($blockinfo['content']);
    // Defaults
    if (empty($vars['addtopics'])) {
        $vars['addtopics'] = 'on';
    }
    if (empty($vars['addposts'])) {
        $vars['addposts'] = 'on';
    }
    if (empty($vars['howmany'])) {
        $vars['howmany'] = 10;
    }
    if (empty($vars['forumid'])) {
        $vars['forumid'] = Array('all');
    }
    if (!isset($vars['addauthor'])) {
        $vars['addauthor'] = '2';
    }
    if (!isset($vars['addlink'])) {
        $vars['addlink'] = '2';
    }
    if (!isset($vars['addobject'])) {
        $vars['addobject'] = '1';
    }
    if (empty($vars['adddate'])) {
        $vars['adddate'] = 'on';
    }
    if (empty($vars['truncate'])) {
        $vars['truncate'] = 20;
    }
    if (!isset($vars['forumlink'])) {
        $vars['forumlink'] = '2';
    }
/*  Later - need to make sure 'modified' and linebreaks are not captured
    if (empty($vars['titleortext'])) {
        $vars['titleortext'] = 'on';
    }
*/

    // get the list of modules+itemtypes that comments is hooked to
    $hookedmodules = xarModAPIFunc('modules', 'admin', 'gethookedmodules',
                                   array('hookModName' => 'comments'));

    $forumlist = array();
    $forumnum=array();
    $forumlist['all'] = xarML('All Forums');
    $modname='xarbb';

            // Get the list of all item types for this module (if any)
            $mytypes = xarModAPIFunc('xarbb','user','getitemtypes');
            // we have hooks for individual item types here
            if (!isset($mytypes[0])) {
                foreach ($mytypes as $itemtype => $val) {
                    if (isset($mytypes[$itemtype])) {
                        $type = $mytypes[$itemtype]['label'];
                    } else {
                        $type = xarML('type #(1)',$itemtype);
                    }
                    $forumlist["$itemtype"] = ' - ' . $type;
                }
            } else {
                $forumlist[$modname] = ucwords($modname);
                // allow selecting individual item types here too (if available)
                if (!empty($mytypes) && count($mytypes) > 0) {
                    foreach ($mytypes as $itemtype => $mytype) {
                        if (!isset($mytype['label'])) continue;
                        $forumlist["$itemtype"] = ' - ' . $mytype['label'];
                    }
                }

            }


    // Send content to template
    $output =  array(
                                'addtopics' => $vars['addtopics'],
                                'addposts'  => $vars['addposts'],
                                'howmany'   => $vars['howmany'],
                                'forumid'   => $vars['forumid'],
                                'forumlist' => $forumlist,
                                'addauthor' => $vars['addauthor'],
                                'addlink'   => $vars['addlink'],
                                'addobject' => $vars['addobject'],
                                'adddate'   => $vars['adddate'],
                                //'titleortext' => $vars['titleortext'],
                                'truncate'  => $vars['truncate'],
                                'forumlink' => $vars['forumlink']
                                );

    // Return output
    return $output;
}

/**
 * update block settings
 */
function xarbb_latestpostsblock_update($blockinfo)
{
    $vars['addtopics']   = xarVarCleanFromInput('addtopics');
    $vars['addposts']    = xarVarCleanFromInput('addposts');
    $vars['howmany']     = xarVarCleanFromInput('howmany');
    $vars['forumid']     = xarVarCleanFromInput('forumid');
    $vars['addauthor']   = xarVarCleanFromInput('addauthor');
    $vars['addlink']     = xarVarCleanFromInput('addlink');
    $vars['addobject']   = xarVarCleanFromInput('addobject');
    $vars['adddate']     = xarVarCleanFromInput('adddate');
//    $vars['titleortext'] = xarVarCleanFromInput('titleortext');
    $vars['truncate']    = xarVarCleanFromInput('truncate');
    $vars['forumlink']   = xarVarCleanFromInput('forumlink');

    $blockinfo['content'] = serialize($vars);

    return $blockinfo;
}

?>

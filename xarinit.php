<?php
/**
 * File: $Id$
 *
 * Xaraya Markdown
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Markdown Module
 * @author John Cox
*/

function markdown_init()
{
    if (!xarModRegisterHook('item',
                            'transform',
                            'API',
                            'markdown',
                            'user',
                            'transform')) return;
    xarRegisterMask('OverviewMarkdown','All','smilies','All','All','ACCESS_OVERVIEW');
    return true;
}
function markdown_delete()
{
    // Remove module hooks
    if (!xarModUnregisterHook('item',
                              'transform',
                              'API',
                              'markdown',
                              'user',
                              'transform')) return;

    // Remove module variables
    xarModDelAllVars('markdown');
    xarRemoveMasks('markdown');
    xarRemoveInstances('markdown');
    return true;
}
?>
<?php
/**
 * Update the configuration options
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

function encyclopedia_admin_updateconfig()
{
    if(!xarVarFetch('welcome',   'str:1:50', $welcome   , 'This is your welcome message', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('imagewidth','int', $imagewidth   , 200, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('imageheight',   'int', $imageheight   , 200, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('autolinks',   'checkbox', $autolinks   , 0, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('columns',   'int', $columns   , 3, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('layout',   'int', $layout   , 1, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('longdisplay',   'checkbox', $longdisplay   , 0, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('itemsperpage',   'int', $itemsperpage   , 20, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('upload',   'checkbox', $upload   , 0, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('allowsearch',   'checkbox', $allowsearch   , 1, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('allowletters',   'checkbox', $allowletters   , 1, XARVAR_NOT_REQUIRED)) {return;}

    if (!xarSecConfirmAuthKey()) return;

    xarModSetVar('encyclopedia', 'welcome', $welcome);
    xarModSetVar('encyclopedia', 'upload', $upload);
    xarModSetVar('encyclopedia', 'imagewidth', $imagewidth);
    xarModSetVar('encyclopedia', 'imageheight', $imageheight);
    xarModSetVar('encyclopedia', 'autolinks', $autolinks);
    xarModSetVar('encyclopedia', 'columns', $columns);
    xarModSetVar('encyclopedia', 'layout', $layout);
    xarModSetVar('encyclopedia', 'longdisplay', $longdisplay);
    xarModSetVar('encyclopedia', 'itemsperpage', $itemsperpage);
    xarModSetVar('encyclopedia', 'allowsearch', $allowsearch);
    xarModSetVar('encyclopedia', 'allowletters', $allowletters);

    xarResponseRedirect(xarModURL('encyclopedia', 'admin', 'modifyconfig'));
}
?>
<?php
/**
 * File: $Id$
 * 
 * Update configuration parameters of the module with information passed back by the modification form
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
  * @subpackage Realms
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function tinymce_admin_updateconfig()
{
    if (!xarVarFetch('tinymode','str:1:',$tinymode,'textareas',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tinyinstances', 'str:1:', $tinyinstances, 'summary,body,tpost,message', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tinycsslist', 'str:1:', $tinycsslist, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tinytheme','str:1:',$tinytheme,'default',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tinyask','str:1:',$tinyask,'true',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tinyextended','str:1:',$tinyextended,'code,pre,blockquote/quote',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tinyexstyle','str:1:',$tinyexstyle,'heading 1=head1,heading 2=head2,heading 3=head3,heading 4=head4',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tinybuttons','str:1:',$tinybuttons,'',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tinybuttonsremove','str:1:',$tinybuttonsremove,'',XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;
    xarModSetVar('tinymce', 'tinymode', $tinymode);
    xarModSetVar('tinymce', 'tinyask', $tinyask);   
    xarModSetVar('tinymce', 'tinybuttons', $tinybuttons);
    xarModSetVar('tinymce', 'tinybuttonsremove', $tinybuttonsremove);
    xarModSetVar('tinymce', 'tinyextended', $tinyextended);         
    xarModSetVar('tinymce', 'tinyinstances', $tinyinstances);
    xarModSetVar('tinymce', 'tinytheme', $tinytheme);
    xarModSetVar('tinymce', 'tinycsslist', $tinycsslist);
    xarModSetVar('tinymce', 'tinyexstyle', $tinyexstyle);
    xarModCallHooks('module','updateconfig','tinymce',
              array('module' => 'tinymce'));

   xarResponseRedirect(xarModURL('tinymce', 'admin', 'modifyconfig'));

    // Return
    return true;
}

?>

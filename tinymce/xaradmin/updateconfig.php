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
    if (!xarVarFetch('tinytoolbar','str:1:',$tinytoolbar,'',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tinywidth','int:1:',$tinywidth,'',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tinylang','str:1:',$tinylang,'',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tinyinlinestyle','str:1:',$tinyinlinestyle,'true',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tinyundolevel','int:1:3',$tinyundolevel,'10',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('defaulteditor','str:1:',$defaulteditor,'',XARVAR_NOT_REQUIRED)) return;
    if (!xarSecConfirmAuthKey()) return;
    //set mode to all textareas for now
    xarModSetVar('tinymce', 'tinymode', $tinymode);
    xarModSetVar('tinymce', 'tinyask', $tinyask);
    xarModSetVar('tinymce', 'tinybuttons', $tinybuttons);
    xarModSetVar('tinymce', 'tinybuttonsremove', $tinybuttonsremove);
    xarModSetVar('tinymce', 'tinyextended', $tinyextended);         
    xarModSetVar('tinymce', 'tinyinstances', $tinyinstances);
    xarModSetVar('tinymce', 'tinytheme', $tinytheme);
    xarModSetVar('tinymce', 'tinylang', $tinylang);    
    xarModSetVar('tinymce', 'tinycsslist', $tinycsslist);
    xarModSetVar('tinymce', 'tinyexstyle', $tinyexstyle);
    xarModSetVar('tinymce', 'tinytoolbar', $tinytoolbar);
    xarModSetVar('tinymce', 'tinywidth', $tinywidth);
    xarModSetVar('tinymce', 'tinyinlinestyle',$tinyinlinestyle);
    xarModSetVar('tinymce', 'tinyundolevel',$tinyundolevel);
    xarModSetVar('base','editor', $defaulteditor);
    
    //Turn our settings into javascript for insert into template
    //Let's call the variable jstext
    $jstext='';
    //start the string
    $jstext = 'mode : "'.xarModGetVar('tinymce','tinymode').'",';

    $jstext .='theme : "'.xarModGetVar('tinymce','tinytheme').'",';

    if (xarModGetVar('tinymce','tinytheme') =='advanced') {
    //set a few advanced theme options
        $jstext .='theme_advanced_toolbar_location : "'.xarModGetVar('tinymce','tinytoolbar').'",';

    // $jstext .= 'theme_advanced_styles : "'.trim(xarModGetVar('tinymce','tinyexstyle')).'",';

        if (trim(xarModGetVar('tinymce','tinycsslist')) <> '') {
            $jstext .='content_css : "'.xarModGetVar('tinymce','tinycsslist').'",';
        }
        if (trim(xarModGetVar('tinymce','tinybuttonsremove')) <> '') {
            $jstext .='theme_advanced_disable : "'.trim(xarModGetVar('tinymce','tinybuttonsremove')).'",';
        }
        if (trim(xarModGetVar('tinymce','tinybuttons')) <> '') {
          $jstext .='theme_advanced_buttons3 : "'.trim(xarModGetVar('tinymce','tinybuttons')).'",';
        }
    }
    if (xarModGetVar('tinymce','tinywidth') > 0) {
        $jstext .='width : "'.xarModGetVar('tinymce','tinywidth').'px",';
    }
    //Setup for 'exact' mode - we only want to replace areas that match for id or name
    if ((xarModGetVar('tinymce','tinymode') =='exact') and (trim(xarModGetVar('tinymce','tinyinstances')) <> '')){
      $elementlist=explode(',',xarModGetVar('tinymce','tinyinstances'));
            $jstext .='elements : "'.xarModGetVar('tinymce','tinyinstances').'",';
    }
    if (trim(xarModGetVar('tinymce','tinyextended')) <> '') {
        $jstext .='extended_valid_elements : "'.xarModGetVar('tinymce','tinyextended').'",';
    }
    if (xarModGetVar('tinymce','tinyask')){
        $jstext .='ask : "true",';
    }
   if (xarModGetVar('tinymce','tinyinlinestyle')){
        $jstext .='inline_styles : "'.xarModGetVar('tinymce','tinyinlinestyle').'",';
    }
   if (xarModGetVar('tinymce','tinyundolevel') > 0){
        $jstext .='custom_undo_redo_levels : "'.xarModGetVar('tinymce','tinyundolevel').'",';
    }
    //add known requirement last to ensure proper syntax with no trailing comma
    $jstext .='language : "'.xarModGetVar('tinymce','tinylang').'"';

    //let's set the var to hold the js text
    xarModSetVar('tinymce','jstext',$jstext);

    xarModCallHooks('module','updateconfig','tinymce',
              array('module' => 'tinymce'));

   xarResponseRedirect(xarModURL('tinymce', 'admin', 'modifyconfig'));

    // Return
    return true;
}

?>

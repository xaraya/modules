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
    if (!xarSecConfirmAuthKey()) return;
    if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'basic', XARVAR_NOT_REQUIRED)) return;
    switch ($data['tab']) {
        case 'basic':
            if (!xarVarFetch('defaulteditor','str:1:',$defaulteditor,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinymode','str:1:',$tinymode,'specific_textareas',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinyloadmode','str:1:',$tinyloadmode,'manual',XARVAR_NOT_REQUIRED)) return;
                xarModSetVar('base','editor', $defaulteditor);
                xarModSetVar('tinymce', 'tinymode', $tinymode);
                xarModSetVar('tinymce', 'tinyloadmode', $tinyloadmode);

            break;
        case 'general':
            if (!xarVarFetch('tinytheme','str:1:',$tinytheme,'advanced',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinyask','str:1:',$tinyask,'true',XARVAR_NOT_REQUIRED)) return;
            // We have fancy validation rules to avoid writing the same code over and over
            // This one: treats the string as a comma-separeted list of tokens. Each token
            // is lower-cased, trimmed and compared to a list of allowed browsers.
            if (!xarVarFetch('tinybrowsers', 'strlist:,:pre:lower:trim:passthru:enum:msie:gecko:safari', $tinybrowsers, 'msie,gecko,safari', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('usebutton','str:1:',$usebutton,'false',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinyundolevel','int:1:3',$tinyundolevel,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinydirection','str:1:3',$tinydirection,'ltr',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinyinstances', 'str:1:', $tinyinstances, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinywidth','str:1:',$tinywidth,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinyheight','str:1:',$tinyheight,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinylang','str:1:',$tinylang,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinybr','str:1:',$tinybr,'false',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinynowrap','str:1:',$tinynowrap,'false',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinypara','str:1:',$tinypara,'false',XARVAR_NOT_REQUIRED)) return;

               xarModSetVar('tinymce', 'tinytheme', $tinytheme);
               xarModSetVar('tinymce', 'tinyask', $tinyask);
               xarModSetVar('tinymce', 'tinyundolevel',$tinyundolevel);
               xarModSetVar('tinymce','tinydirection', $tinydirection);
               xarModSetVar('tinymce', 'tinyinstances', $tinyinstances); //not used at this stage
               xarModSetVar('tinymce', 'tinywidth', $tinywidth);
               xarModSetVar('tinymce', 'tinyheight', $tinyheight);
               xarModSetVar('tinymce', 'tinylang', $tinylang);
               xarModSetVar('tinymce', 'tinybr', $tinybr);
               xarModSetVar('tinymce', 'tinypara', $tinypara);
               xarModSetVar('tinymce', 'tinynowrap', $tinynowrap);
               xarModSetVar('tinymce', 'usebutton', $usebutton);
               xarModSetVar('tinymce', 'tinybrowsers', $tinybrowsers);

            break;
        case 'cssplug':
            if (!xarVarFetch('tinyextended','str:1:',$tinyextended,'code,pre,blockquote/quote',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinyinlinestyle','str:1:',$tinyinlinestyle,'true',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinyplugins','str:1:',$tinyplugins,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinyencode','str:0:',$tinyencode,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinycsslist', 'str:1:', $tinycsslist, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinydate', 'str:1:', $tinydate, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinytime', 'str:1:', $tinytime, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinyinvalid', 'str:1:', $tinyinvalid, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('useibrowser','int:1:',$useibrowser,0,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('editorcss','str:1:',$editorcss,'',XARVAR_NOT_REQUIRED)) return;
                 xarModSetVar('tinymce', 'tinyextended', $tinyextended);
                xarModSetVar('tinymce', 'tinyinlinestyle',$tinyinlinestyle);
                xarModSetVar('tinymce','tinyencode', $tinyencode); //not used at this stage
                xarModSetVar('tinymce','tinyplugins', $tinyplugins);
                xarModSetVar('tinymce', 'tinycsslist', $tinycsslist);
                xarModSetVar('tinymce','tinydate', $tinydate);
                xarModSetVar('tinymce','tinytime', $tinytime);
                xarModSetVar('tinymce','tinyinvalid', $tinyinvalid);
                xarModSetVar('tinymce', 'useibrowser', $useibrowser);
               xarModSetVar('tinymce', 'tinyeditorcss', $editorcss);

            break;
        case 'customadvanced':
            if (!xarVarFetch('tinytoolbar','str:1:',$tinytoolbar,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinybuttons','str:1:',$tinybuttons,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinybuttons2','str:1:',$tinybuttons2,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinybuttons3','str:1:',$tinybuttons3,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinybuttonsremove','str:1:',$tinybuttonsremove,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinyexstyle','str:1:',$tinyexstyle,'heading 1=head1,heading 2=head2,heading 3=head3,heading 4=head4',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinybuild1','str:1:',$tinybuild1,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinybuild2','str:1:',$tinybuild2,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinybuild3','str:1:',$tinybuild3,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinyadvformat','str:1:',$tinyadvformat,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinyshowpath','str:1:',$tinyshowpath,'',XARVAR_NOT_REQUIRED)) return;
               xarModSetVar('tinymce', 'tinyexstyle', $tinyexstyle); //not used at this stage
               xarModSetVar('tinymce', 'tinytoolbar', $tinytoolbar);
               xarModSetVar('tinymce', 'tinybuttons', $tinybuttons);
               xarModSetVar('tinymce', 'tinybuttons2', $tinybuttons2);
               xarModSetVar('tinymce', 'tinybuttons3', $tinybuttons3);
               xarModSetVar('tinymce', 'tinybuild1', $tinybuild1);
               xarModSetVar('tinymce', 'tinybuild2', $tinybuild2);
               xarModSetVar('tinymce', 'tinybuild3', $tinybuild3);
               xarModSetVar('tinymce', 'tinyadvformat', $tinyadvformat);
               xarModSetVar('tinymce', 'tinyshowpath', $tinyshowpath);
               xarModSetVar('tinymce', 'tinybuttonsremove', $tinybuttonsremove);

           break;
    case 'customconfig':
           if (!xarVarFetch('tinycustom','str:1:',$tinycustom,'',XARVAR_NOT_REQUIRED)) return;
           if (!xarVarFetch('dousemulticonfig','checkbox',$dousemulticonfig,'',XARVAR_NOT_REQUIRED)) return;
           if (!xarVarFetch('multiconfig','str:1:',$multiconfig,'',XARVAR_NOT_REQUIRED)) return;
                xarModSetVar('tinymce', 'tinycustom', $tinycustom);
                xarModSetVar('tinymce', 'multiconfig', $multiconfig);
                xarModSetVar('tinymce', 'usemulticonfig', $dousemulticonfig);
    break;

    }

    $tinymode=xarModGetVar('tinymce','tinymode');

    if ($tinymode=='textareas'){
          xarModSetVar('tinymce','usebutton',0);
    }

    $xarbaseurl=xarServerGetBaseURL();
    $tinybasepath="'.$xarbaseurl.'modules/tinymce/xartemplates/includes/tinymce/jscripts/tiny_mce/tiny_mce.js";
   
    //use the image browser? Check configuration
   $useibrowser=xarModGetVar('tinymce','useibrowser');
    if ($useibrowser == 1) { //autoset
        //theme
        xarModSetVar('tinymce', 'tinytheme', 'advanced');
        //plugins
       $currentplugs = xarModGetVar('tinymce', 'tinyplugins');
        if (trim($currentplugs)=='')  {
             $currentplugs= 'ibrowser';
        }elseif (strstr($currentplugs,'ibrowser')==false) {
             $currentplugs='ibrowser,'.$currentplugs;
        }
        xarModSetVar('tinymce','tinyplugins', $currentplugs);

        //remove image button
        $currentremove=  xarModGetVar('tinymce', 'tinybuttonsremove');
        if (trim($currentremove)=='') {
             $currentremove= 'image';
        }elseif (strstr($currentremove,'image')==false) {
             $currentremove='image,'.$currentremove;
        }
        xarModSetVar('tinymce', 'tinybuttonsremove', $currentremove);
       //add ibrowser button
         $currentbutton2=xarModGetVar('tinymce', 'tinybuttons2');
        if (trim($currentbutton2)=='') {
             $currentbutton2= 'ibrowser';
        }elseif (strstr($currentbutton2,'ibrowser')==false) {
             $currentbutton2='ibrowser,'.$currentbutton2;
        }
         xarModSetVar('tinymce', 'tinybuttons2', $currentbutton2);

    }elseif ($useibrowser ==2) {
       //theme - leave as is
        //xarModSetVar('tinymce', 'tinytheme', 'Default');
        //remove plugin
        $currentplugs = xarModGetVar('tinymce', 'tinyplugins');
        if ((trim($currentplugs)<>'') && (strstr($currentplugs,'ibrowser')==true))  {
             $currentplugs= str_replace(array(',ibrowser','ibrowser,','ibrowser'),'',$currentplugs);
        }
        xarModSetVar('tinymce','tinyplugins', $currentplugs);

        //add back image button
        $currentremove=  xarModGetVar('tinymce', 'tinybuttonsremove');
        if ((trim($currentremove)<>'') && (strstr($currentremove,'image')==true)) {
             $currentremove=str_replace(array('image',',image','image,'),'',$currentremove);
        }
        xarModSetVar('tinymce', 'tinybuttonsremove', $currentremove);
       //remove ibrowser button
         $currentbutton2=xarModGetVar('tinymce', 'tinybuttons2');
        if ((trim($currentbutton2)<>'') && (strstr($currentbutton2,'ibrowser')==true)) {
             $currentbutton2=str_replace(array('ibrowser,',',ibrowser','ibrowser'),'',$currentbutton2);
        }
         xarModSetVar('tinymce', 'tinybuttons2', $currentbutton2);
    
    } else {
            xarModSetVar('tinymce','iusebrowse',0);
    }

    //Turn our settings into javascript for insert into template
    //Let's call the variable jstext 


    //start the string
    $jstext = 'mode : "'.xarModGetVar('tinymce','tinymode').'",';
    $jstext .='theme : "'.xarModGetVar('tinymce','tinytheme').'",';
    $jstext .='document_base_url : "'.xarServerGetBaseURL().'",';
    if (trim(xarModGetVar('tinymce','tinycsslist')) <> '') {
           $jstext .='content_css : "'.$xarbaseurl.xarModGetVar('tinymce','tinycsslist').'",';
        }
    if (trim(xarModGetVar('tinymce','tinyeditorcss')) <> '') {
           $jstext .='editor_css : "'.$xarbaseurl.xarModGetVar('tinymce','tinyeditorcss').'",';
        }

    if (xarModGetVar('tinymce','tinywidth') > 0) {
        $jstext .='width : "'.xarModGetVar('tinymce','tinywidth').'", ';
    }
    if (xarModGetVar('tinymce','tinyheight') > 0) {
        $jstext .='height : "'.xarModGetVar('tinymce','tinyheight').'", ';
    }

    $browserstring=xarModGetVar('tinymce','tinybrowsers');

    if ($browserstring=='msie,gecko,safari' || $browserstring=='') {
    /* do not include anything */
    }else{
       $jstext .='browsers : "'.xarModGetVar('tinymce','tinybrowsers').'", ';
    }
    if (trim(xarModGetVar('tinymce','tinyplugins')) <> '') {
        $jstext .='plugins : "'.xarModGetVar('tinymce','tinyplugins').'", ';
    }

    if (xarModGetVar('tinymce','tinyask')=='true'){
        $jstext .='ask : "true",';
    }
    if (xarModGetVar('tinymce','tinyinlinestyle')){
        $jstext .='inline_styles : "'.xarModGetVar('tinymce','tinyinlinestyle').'", ';
    }
    if (xarModGetVar('tinymce','tinyundolevel') > 0){
        $jstext .='custom_undo_redo_levels : "'.xarModGetVar('tinymce','tinyundolevel').'", ';
    }
    if (xarModGetVar('tinymce','tinybr')=='true'){
        $jstext .='force_br_newlines: "true",';
    }
    if (xarModGetVar('tinymce','tinynowrap')=='true'){
        $jstext .='nowrap: "true",';
    }
    if (xarModGetVar('tinymce','tinypara')=='true'){
        $jstext .='force_p_newlines: "true",';
    }
   if (trim(xarModGetVar('tinymce','tinyinvalid')) <> '') {
          $jstext .='invalid_elements  : "'.trim(xarModGetVar('tinymce','tinyinvalid')).'", ';
    }
    if (trim(xarModGetVar('tinymce','tinydate')) <> '') {
          $jstext .='plugin_insertdate_dateFormat  : "'.trim(xarModGetVar('tinymce','tinydate')).'", ';
    }
    if (trim(xarModGetVar('tinymce','tinytime')) <> '') {
          $jstext .='plugin_insertdate_timeFormat  : "'.trim(xarModGetVar('tinymce','tinytime')).'", ';
    }
    if (xarModGetVar('tinymce','tinytheme') =='advanced') {
    //set a few advanced theme options
        $jstext .='theme_advanced_toolbar_location : "'.xarModGetVar('tinymce','tinytoolbar').'", ';
        $jstext .='theme_advanced_path_location: "'.xarModGetVar('tinymce','tinyshowpath').'", ';

     // $jstext .= 'theme_advanced_styles : "'.trim(xarModGetVar('tinymce','tinyexstyle')).'",';

        if (trim(xarModGetVar('tinymce','tinybuttonsremove')) <> '') {
            $jstext .='theme_advanced_disable : "'.trim(xarModGetVar('tinymce','tinybuttonsremove')).' ",';
        }
        if (trim(xarModGetVar('tinymce','tinybuttons')) <> '') {
          $jstext .='theme_advanced_buttons1_add : "'.trim(xarModGetVar('tinymce','tinybuttons')).'", ';
        }
        if (trim(xarModGetVar('tinymce','tinybuttons2')) <> '') {
          $jstext .='theme_advanced_buttons2_add : "'.trim(xarModGetVar('tinymce','tinybuttons2')).'", ';
        }
        if (trim(xarModGetVar('tinymce','tinybuttons3')) <> '') {
          $jstext .='theme_advanced_buttons3_add : "'.trim(xarModGetVar('tinymce','tinybuttons3')).'", ';
        }
        if (trim(xarModGetVar('tinymce','tinybuild1')) <> '') {
          $jstext .='theme_advanced_buttons1 : "'.xarModGetVar('tinymce','tinybuild1').'", ';
        }
        if (trim(xarModGetVar('tinymce','tinybuild2')) <> '') {
          $jstext .='theme_advanced_buttons2 : "'.xarModGetVar('tinymce','tinybuild2').'",';
        }
        if (trim(xarModGetVar('tinymce','tinybuild3')) <> '') {
          $jstext .='theme_advanced_buttons3 : "'.xarModGetVar('tinymce','tinybuild3').'",';
        }
  //      $jstext .= 'debug : "true",';
        if (trim(xarModGetVar('tinymce','tinyadvformat')) <> '') {
          $jstext .='theme_advanced_blockformats : "'.xarModGetVar('tinymce','tinyadvformat').'", ';
        }
   }

    //Setup for 'exact' mode - we only want to replace areas that match for id or name
    if ((xarModGetVar('tinymce','tinymode') =='exact') and (trim(xarModGetVar('tinymce','tinyinstances')) <> '')){
      $elementlist=explode(',',xarModGetVar('tinymce','tinyinstances'));
            $jstext .='elements : "'.xarModGetVar('tinymce','tinyinstances').'", ';
    }
    if (trim(xarModGetVar('tinymce','tinyextended')) <> '') {
        $jstext .='extended_valid_elements : "'.xarModGetVar('tinymce','tinyextended').'", ';
    }

    if (xarModGetVar('tinymce','tinyencode')){
        $jstext .='encoding : "'.xarModGetVar('tinymce','tinyencode').'", ';
    }
    if (strlen(trim(xarModGetVar('tinymce','tinycustom')))>0) {
       $jstext .=xarModGetVar('tinymce','tinycustom');
    }

   // $jstext .='force_br_newlines : "",';    //works only for IE at the moment
    $jstext .='directionality : "'.xarModGetVar('tinymce','tinydirection').'",';
    //add known requirement last to ensure proper syntax with no trailing comma
    $jstext .='language : "'.xarModGetVar('tinymce','tinylang').'" ';

    //now add the other configurations
    if (xarModGetVar('tinymce','usemulticonfig')){
        if (strlen(trim(xarModGetVar('tinymce','multiconfig')))>0) {
          $multiconfig =xarModGetVar('tinymce','multiconfig');
        }
    }else{
          $multiconfig='';
    }
    
    //let's set button or popup
    $buttonon=xarML('Turn On');
    $buttonoff=xarML('Turn Off');
    if (xarModGetVar('tinymce','usebutton') == 'true' && xarModGetVar('tinymce','tinymode') =='specific_textareas') {
       $buttonswitch  = 'function mce_button_toggle(form_element_id, button_o)';
       $buttonswitch .= ' { if(editor_id = tinyMCE.getEditorId(form_element_id)) {';
       $buttonswitch .= 'tinyMCE.removeMCEControl(editor_id);';
       $buttonswitch .= 'button_o.value = "'.$buttonon.'";';
       $buttonswitch .= '    } else {';
       $buttonswitch .= ' tinyMCE.addMCEControl(document.getElementById(form_element_id), form_element_id);';
       $buttonswitch .= ' button_o.value = "'.$buttonoff.'";';
       $buttonswitch .= '    }';
       $buttonswitch .= 'return false;';
       $buttonswitch .= '}';
    }else{
       $buttonswitch='';
    }
    //let's set the var to hold the js text
    xarModSetVar('tinymce','jstext',$jstext);
    xarModSetVar('tinymce','multiconfig',$multiconfig);
    xarModSetVar('tinymce','buttonstring',$buttonswitch);

    xarModCallHooks('module','updateconfig','tinymce',
              array('module' => 'tinymce'));

   xarResponseRedirect(xarModURL('tinymce', 'admin', 'modifyconfig',array('tab' => $data['tab'])));

    return true;
}

?>

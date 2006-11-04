<?php
/**
 * Update of Configuration 
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xartinymce module
 * @link http://xaraya.com/index.php/release/63.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/**
 * Update configuration parameters to relevant vars in database
 *
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
            if (!xarVarFetch('tinymode','str:1:',$tinymode,'textareas',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinyloadmode','str:1:',$tinyloadmode,'auto',XARVAR_NOT_REQUIRED)) return;
                xarModSetVar('base','editor', $defaulteditor);
                xarModSetVar('tinymce', 'tinymode', $tinymode);
                xarModSetVar('tinymce', 'tinyloadmode', $tinyloadmode);

            break;
        case 'general':
            if (!xarVarFetch('tinytheme','str:1:',$tinytheme,'default',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinyask','checkbox',$tinyask,1,XARVAR_NOT_REQUIRED)) return;
            /*  We have fancy validation rules to avoid writing the same code over and over
             * This one: treats the string as a comma-separeted list of tokens. Each token
             * is lower-cased, trimmed and compared to a list of allowed browsers.
             */
            if (!xarVarFetch('tinybrowsers', 'strlist:,:pre:lower:trim:passthru:enum:msie:gecko:safari:opera', $tinybrowsers, 'msie,gecko,safari', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('usebutton','checkbox',$usebutton,null,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinyundolevel','int:1:3',$tinyundolevel,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinydirection','str:1:3',$tinydirection,'ltr',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinyinstances', 'str:1:', $tinyinstances, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinywidth','int:1:',$tinywidth,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinyheight','int:1:',$tinyheight,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinylang','str:1:',$tinylang,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinybr','checkbox',$tinybr,false,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('stripbreaks','checkbox',$stripbreaks,true,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('sourceformat','checkbox',$sourceformat,false,XARVAR_NOT_REQUIRED)) return;
            //if (!xarVarFetch('tinynowrap','str:1:',$tinynowrap,'false',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinypara','checkbox',$tinypara,false,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinytilemap','checkbox',$tinytilemap,true,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinyeditorselector','str:1:',$tinyeditorselector,'mceEditor',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinyeditordeselector','str:1:',$tinyeditordeselector,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinycleanup','checkbox',$tinycleanup,true,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinycompressor','checkbox',$tinycompressor,false,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinyentity_encoding','str:1:',$tinyentity_encoding,'raw',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinyencode','checkbox',$tinyencode,false,XARVAR_NOT_REQUIRED)) return;
            //if (!xarVarFetch('tinyentities','str:0:',$tinyentities,'',XARVAR_NOT_REQUIRED)) return; Not specifically used - default string is used if entity_encoding is set to 'named'
               xarModSetVar('tinymce', 'tinytheme', $tinytheme);
               xarModSetVar('tinymce', 'tinyask', ($tinyask?1:0));
               xarModSetVar('tinymce', 'striplinebreaks', ($stripbreaks?1:0));
               xarModSetVar('tinymce', 'tinyundolevel',$tinyundolevel);
               xarModSetVar('tinymce', 'tinydirection', $tinydirection);
               xarModSetVar('tinymce', 'tinyinstances', $tinyinstances); //not used at this stage
               xarModSetVar('tinymce', 'tinywidth', $tinywidth);
               xarModSetVar('tinymce', 'tinyheight', $tinyheight);
               xarModSetVar('tinymce', 'tinylang', $tinylang);
               xarModSetVar('tinymce', 'tinybr', ($tinybr?1:0));
               xarModSetVar('tinymce', 'tinypara', ($tinypara?1:0));
               xarModSetVar('tinymce', 'sourceformat', ($sourceformat?1:0));
              // xarModSetVar('tinymce', 'tinynowrap', $tinynowrap);
               xarModSetVar('tinymce', 'tinycompressor',($tinycompressor?1:0));
               xarModSetVar('tinymce', 'tinycleanup',($tinycleanup?1:0));
               xarModSetVar('tinymce', 'usebutton', ($usebutton?1:0));
               xarModSetVar('tinymce', 'tinybrowsers', $tinybrowsers);
               xarModSetVar('tinymce', 'tinytilemap', ($tinytilemap?1:0));
               xarModSetVar('tinymce','tinyencode', ($tinyencode?1:0));
               xarModSetVar('tinymce','tinyentity_encoding', $tinyentity_encoding);
               $tinyeditorselector = trim($tinyeditorselector);
               if ($tinyeditorselector ==''){
                   xarModSetVar('tinymce','tinyeditorselector','mceEditor');
               } else {
                   xarModSetVar('tinymce','tinyeditorselector',$tinyeditorselector);
               }
               $tinyeditordeselector = trim($tinyeditordeselector);
               xarModSetVar('tinymce', 'tinyeditordeselector', $tinyeditordeselector);

            break;
        case 'cssplug':
            if (!xarVarFetch('tinyextended','str:1:',$tinyextended,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinyinlinestyle','checkbox',$tinyinlinestyle,true,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinycsslist', 'str:1:', $tinycsslist, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinydate', 'str:1:', $tinydate, '', XARVAR_NOT_REQUIRED)) return;
            //if (!xarVarFetch('useibrowser','int:1:',$useibrowser,0,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('editorcss','str:1:',$editorcss,'',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinyinvalid', 'str:1:', $tinyinvalid, '', XARVAR_NOT_REQUIRED)) return;
                xarModSetVar('tinymce', 'tinyextended', $tinyextended);
                xarModSetVar('tinymce', 'tinyinlinestyle',$tinyinlinestyle);
                xarModSetVar('tinymce', 'tinycsslist', $tinycsslist);
                xarModSetVar('tinymce','tinyinvalid', $tinyinvalid);
                /* xarModSetVar('tinymce', 'useibrowser', $useibrowser); deprecated version 0.9.2 */
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
            if (!xarVarFetch('tinyadvresize','checkbox',$tinyadvresize,true,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('usefilebrowser','checkbox',$usefilebrowser,false,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinyresizehorizontal','checkbox',$tinyresizehorizontal,false,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinyenablepath','str:1:',$tinyenablepath,1,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinytime', 'str:1:', $tinytime, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinydate', 'str:1:', $tinydate, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tinyplugins','str:1:',$tinyplugins,'',XARVAR_NOT_REQUIRED)) return;

               xarModSetVar('tinymce','tinyplugins', $tinyplugins);
               xarModSetVar('tinymce','tinydate', $tinydate);
               xarModSetVar('tinymce','tinytime', $tinytime);
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
               xarModSetVar('tinymce','tinyadvresize', ($tinyadvresize?1:0));
               xarModSetVar('tinymce','tinyenablepath', $tinyenablepath);
               xarModSetVar('tinymce','tinyresizehorizontal', ($tinyresizehorizontal?1:0));
               xarModSetVar('tinymce','usefilebrowser',($usefilebrowser?1:0));
           break;
    case 'customconfig':
           if (!xarVarFetch('tinycustom','str:1:',$tinycustom,'',XARVAR_NOT_REQUIRED)) return;
           if (!xarVarFetch('dousemulticonfig','checkbox',$dousemulticonfig,0,XARVAR_NOT_REQUIRED)) return;
           if (!xarVarFetch('multiconfig','str:1:',$multiconfig,XARVAR_NOT_REQUIRED)) return;
                xarModSetVar('tinymce', 'tinycustom', $tinycustom);
                xarModSetVar('tinymce', 'multiconfig', $multiconfig);
                xarModSetVar('tinymce', 'usemulticonfig', ($dousemulticonfig?1:0));

    break;

    }

    $tinymode=xarModGetVar('tinymce','tinymode');

    if ($tinymode=='textareas'){
          xarModSetVar('tinymce','usebutton',0);
    }

    $xarbaseurl=xarServerGetBaseURL();
    //This is not used anymore we use the loading in the include template
    $tinybasepath="'.$xarbaseurl.'modules/tinymce/xarincludes/tiny_mce.js";
    //Calculate baseurl
    if (strlen(xarCore_getSystemVar('BaseModURL',true))==0) {
        $basemodurl='index.php';
    } else {
        $basemodurl=xarCore_getSystemVar('BaseModURL',true);
    }
    /* Turn our settings into javascript for insert into template
     *Let's call the variable jstext
     */

    /* start building the string */
    $tinymode = xarModGetVar('tinymce','tinymode');
    $jstext = 'mode : "'.$tinymode.'",';
    $jstext .='theme : "'.xarModGetVar('tinymce','tinytheme').'",';
    //$jstext .='document_base_url : "'.xarServerGetBaseURL().'",';

    $jstext .='document_base_url : "'.xarServerGetBaseURL().$basemodurl.'",';
        
    $tinyeditorselector = xarModGetVar('tinymce','tinyeditorselector');

    $tinyeditorselector=isset($tinyeditorselector) ? $tinyeditorselector: 'mceEditor';

    if (($tinymode == 'specific_textareas') &&  empty($tinyeditorselector)) {
        $jstext .='editor_selector : "mceEditor",';
    } elseif (($tinymode == 'specific_textareas') && !empty($tinyeditorselector)){
        $jstext .='editor_selector : "'.$tinyeditorselector.'",';
    }
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

    if (xarModGetVar('tinymce','tinyask')==1){ //defaults to false
        $jstext .='ask : "true",';
    }
    if (xarModGetVar('tinymce','tinyinlinestyle')==1){
        $jstext .='inline_styles : "true", ';
    }
    if (xarModGetVar('tinymce','tinyundolevel') > 0){
        $jstext .='custom_undo_redo_levels : "'.xarModGetVar('tinymce','tinyundolevel').'", ';
    }
   /* deprecated 1.0.1
    if (xarModGetVar('tinymce','tinynowrap')==1){
        $jstext .='nowrap: true,';
    }
    */
    if (xarModGetVar('tinymce','tinytilemap')==1){
        $jstext .='button_tile_map : "true",';
    }
    if (xarModGetVar('tinymce','tinycleanup')!=1){
        $jstext .='cleanup: "false",';
    }
    //else{
    // $jstext .='cleanup: true,';
    //}
    // $jstext .=	'safari_warning: "false",'; now false by default


    if (xarModGetVar('tinymce','tinybr')==1){
        //$jstext .='force_br_newlines: true,';
        $jstext .='convert_newlines_to_brs: "true",';
    }

    if (xarModGetVar('tinymce','tinypara') !=1){ //This is true by default in tinymce
        $jstext .='force_p_newlines: "false",';
    }

    if (xarModGetVar('tinymce','sourceformat')==1){
        $jstext .='apply_source_formatting: "true",';
    }
    if (xarModGetVar('tinymce','striplinebreaks')==1){
        $jstext .='remove_linebreaks: "true",';
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
    if (xarModGetVar('tinymce','tinytheme') <>'simple') {
        
         $jstext .='theme_advanced_toolbar_location: "'.xarModGetVar('tinymce','tinytoolbar').'", ';


        if (xarModGetVar('tinymce','tinyenablepath')!=1){
            $jstext .='theme_advanced_path: "false", ';
        } else { /* if not false set the status path location and resizing */
            $jstext .='theme_advanced_statusbar_location: "'.xarModGetVar('tinymce','tinyshowpath').'", ';
         
            if (xarModGetVar('tinymce','tinyadvresize')==1 ){
                $jstext .='theme_advanced_resizing : "true", ';
            }
            if (xarModGetVar('tinymce','tinyresizehorizontal')==1 ){
                $jstext .='theme_advanced_resize_horizontal : "true", ';
            }
        }


        if (trim(xarModGetVar('tinymce','tinyplugins')) <> '') {
           /* get rid of all white space first */
           $plugs = xarModGetVar('tinymce','tinyplugins');
           $plugs = preg_replace('/[ |\s]*\s+/','',$plugs);
          //  $jstext .='plugins : "'.xarModGetVar('tinymce','tinyplugins').'", ';
            $jstext .='plugins : "'.$plugs.'", ';
        }

        /* $jstext .= 'theme_advanced_styles : "'.trim(xarModGetVar('tinymce','tinyexstyle')).'",'; */

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
        /* Do not trim these build vars - a space will render the line blank */
        if (xarModGetVar('tinymce','tinybuild1') <> '') {
          $jstext .='theme_advanced_buttons1 : "'.xarModGetVar('tinymce','tinybuild1').'", ';
        }
        if (xarModGetVar('tinymce','tinybuild2') <> '') {
          $jstext .='theme_advanced_buttons2 : "'.xarModGetVar('tinymce','tinybuild2').'",';
        }
        if (xarModGetVar('tinymce','tinybuild3') <> '') {
          $jstext .='theme_advanced_buttons3 : "'.xarModGetVar('tinymce','tinybuild3').'",';
        }

       /*  Uncomment to get a debug popup dialog showing paths used
         $jstext .= 'debug : true,';
       */
       if (trim(xarModGetVar('tinymce','tinyadvformat')) <> '') {
           /* get rid of all white space first */
           $advformat = xarModGetVar('tinymce','tinyadvformat');
           $advformat = preg_replace('/[ |\s]*\s+/','',$advformat);
           //$jstext .='theme_advanced_blockformats : "'.xarModGetVar('tinymce','tinyadvformat').'", ';
            $jstext .='theme_advanced_blockformats : "'.$advformat.'", ';
        }
        if (trim(xarModGetVar('tinymce','tinyextended')) <> '') {
           /* get rid of all white space first */
           $extended = xarModGetVar('tinymce','tinyextended');
           $extended = preg_replace('/[ |\s]*\s+/','',$extended);
            //$jstext .='extended_valid_elements : "'.xarModGetVar('tinymce','tinyextended').'", ';
            $jstext .='extended_valid_elements : "'.$extended.'", ';
        }
   }

    /* Setup for 'exact' mode - we only want to replace areas that match for id or name */
    if ((xarModGetVar('tinymce','tinymode') =='exact') and (trim(xarModGetVar('tinymce','tinyinstances')) <> '')){
      $elementlist=explode(',',xarModGetVar('tinymce','tinyinstances'));
            $jstext .='elements : "'.xarModGetVar('tinymce','tinyinstances').'", ';
    }

    if (strlen(trim(xarModGetVar('tinymce','tinycustom')))>0) {
       $jstext .=xarModGetVar('tinymce','tinycustom');
    }
     if (xarModGetVar('tinymce','tinydirection') != 'ltr') { //defaults to ltr
         $jstext .='directionality : "'.xarModGetVar('tinymce','tinydirection').'",';
     }
    /* add our filebrowsercallback file */
    if (xarModGetVar('tinymce','usefilebrowser')==1 ){
        $jstext .='file_browser_callback : "xarfilebrowsercallback",';
    }
    /*language options */
    if (xarModGetVar('tinymce','tinyencode')==1){
        $jstext .='encoding : "xml", ';
    }
    $entity_encoding=xarModGetVar('tinymce','tinyentity_encoding');
    if (!isset($entity_encoding) || empty($entity_encoding)) {
        $jstext .='entity_encoding : "raw", ';
    }else {
         $jstext .='entity_encoding : "'.$entity_encoding.'",';
    }
    /* add known requirement last to ensure proper syntax with no trailing comma */
    $jstext .='language : "'.xarModGetVar('tinymce','tinylang').'" ';
    


    /* now add the other configurations */
    if (xarModGetVar('tinymce','usemulticonfig')==1){
        if (strlen(trim(xarModGetVar('tinymce','multiconfig')))>0) {
          $multiconfig =xarModGetVar('tinymce','multiconfig');
        }
    }else{
          $multiconfig='';
    }

    /* let's set button or popup */
    $buttonon=xarML('Turn On');
    $buttonoff=xarML('Turn Off');
    if (xarModGetVar('tinymce','usebutton') == 1 && xarModGetVar('tinymce','tinymode') =='specific_textareas') {
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
    /* let's set the var to hold the js text */
    xarModSetVar('tinymce','jstext',$jstext);
    xarModSetVar('tinymce','multiconfig',$multiconfig);
    xarModSetVar('tinymce','buttonstring',$buttonswitch);

    xarModCallHooks('module','updateconfig','tinymce',
              array('module' => 'tinymce'));

   xarResponseRedirect(xarModURL('tinymce', 'admin', 'modifyconfig',array('tab' => $data['tab'])));

    return true;
}

?>
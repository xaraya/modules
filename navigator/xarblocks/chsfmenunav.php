<?php
/**
 * chsfmenunav 
 * modification of the base/menu block to
 * use DHTML-Menu for layout
 * Based on:
      - basic menu block of Xaraya
 *    - chsfmenunav-block from the Content Express module for Postnuke 
 *      http://xexpress.sourceforge.net
 *    - DHTML Functionality: 
          HVMenu by Ger Versluis http://www.burmees.nl
 * 
 * CHSF Content Navigation Block
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2004 by the Schwab Foundation
 * @link http://wwwk.schwabfoundation.org
 *
 * @subpackage chsfnav module
 * @author Richard Cave <caveman : rcave@xaraya.com>
*/

/**
 * initialise block
 *
 * @access  public
 * @param   none
 * @return  nothing
 * @throws  no exceptions
 * @todo    nothing
*/
function chsfnav_chsfmenunavblock_init()
{
    return true;
}

/**
 * get information on block
 *
 * @access  public
 * @param   none
 * @return  data array
 * @throws  no exceptions
 * @todo    nothing
*/
function chsfnav_chsfmenunavblock_info()
{
    return array('text_type' => 'CHSFMenu',
         'module' => 'chsfnav',
         'text_type_long' => 'CHSF Javascript Menu',
         'allow_multiple' => false,
         'form_content' => false,
         'form_refresh' => false,
         'show_preview' => true);
}

/**
 * display usermenu block
 *
 * @access  public
 * @param   none
 * @return  data array on success or void on failure
 * @throws  no exceptions
 * @todo    implement centre and right menu position
*/
function chsfnav_chsfmenunavblock_display($blockinfo)
{  
    // Security Check
    if(!xarSecurityCheck('ViewBaseBlocks',0,'Block',"menu:$blockinfo[title]:All")) return;

    $param_cache = xarCoreGetVarDirPath() . "/cache/chsfmenunav_par_$blockinfo[bid].js";
    $cache = xarCoreGetVarDirPath() . "/cache/chsfmenunav_$blockinfo[bid].js";
    
    // Get current content
    $vars = unserialize($blockinfo['content']);

    if (empty($blockinfo['template'])) {
        $template = 'chsfmenunav';
    } else {
        $template = $blockinfo['template'];
    }
        
    $width = xarModGetVar('chsfnav', 'jmbwidth') * $vars['menumaxwidth'];
    $height = xarModGetVar('chsfnav', 'jmbheight') * $vars['menumaxheight'];
      
    //set flag that chsfmenunav block has been defined
    //for conditional include of the main javascript file for the menu at the end of the theme
    xarVarSetCached('Base.blocks','chsfmenunav',true);
        
    $data = xarTplBlock('chsfnav',$template, array('param_cache' => $param_cache,
                                                   'targetloc'   => $vars['targetloc'],
                                                   'cache'       => $cache,
                                                   'width'       => $width,
                                                   'height'      => $height));
              
    // Return data, not rendered content.
    $blockinfo['content'] = $data;

    if (!empty($blockinfo['content'])) {
        return $blockinfo;
    }
}


/**
 * modify block settings
 *
 * @access  public
 * @param   $blockinfo
 * @return  $blockinfo data array
 * @throws  no exceptions
 * @todo    nothing
*/
function chsfnav_chsfmenunavblock_modify($blockinfo)
{
    // Break out options from our content field
    $vars = unserialize($blockinfo['content']);
    $blockinfo['content'] = '';

    // Defaults
    if (empty($vars['menumaxheight'])) {
        $vars['menumaxheight']=20; // Maximum menu height
    }
    if (empty($vars['menumaxwidth'])) {
        $vars['menumaxwidth']=150; // Maximum menu width
    }
    if (empty($vars['lowbgcolor'])) {
        $vars['lowbgcolor']='#FFFFFF'; // Background color when mouse is not over
    }
    if (empty($vars['lowsubbgcolor'])) {
        $vars['lowsubbgcolor']='#FFFFFF'; // Background color when mouse is not over on subs
    }
    if (empty($vars['highbgcolor'])) {
        $vars['highbgcolor']='#000000'; // Background color when mouse is over
    }
    if (empty($vars['highsubbgcolor'])) {
        $vars['highsubbgcolor']='#000000'; // Background color when mouse is over on subs
    }
    if (empty($vars['fontlowcolor'])) {
        $vars['fontlowcolor']='#000000'; // Font color when mouse is not over
    }
    if (empty($vars['fontsublowcolor'])) {
        $vars['fontsublowcolor']='#000000'; // Font color subs when mouse is not over
    }
    if (empty($vars['fonthighcolor'])) {
        $vars['fonthighcolor']='#FFFFFF';  // Font color when mouse is over
    }
    if (empty($vars['fontsubhighcolor'])) {
        $vars['fontsubhighcolor']='#FFFFFF'; // Font color subs when mouse is over
    }
    if (empty($vars['bordercolor'])) {
        $vars['bordercolor']='#000000'; // Border color
    }
    if (empty($vars['bordersubcolor'])) {
        $vars['bordersubcolor']='#000000'; // Border color for subs
    }
    if (empty($vars['borderwidth'])) {
        $vars['borderwidth']=1; // Border width
    }
    if (empty($vars['borderbtwnelmnts'])) {
        $vars['borderbtwnelmnts']=1; // Border between elements 1 or 0
    }
    if (empty($vars['fontfamily'])) {
        $vars['fontfamily']='arial,comic sans ms,technical'; // Font family menu items e.g. 'Verdana'
    }
    if (empty($vars['fontsize'])) {
        $vars['fontsize']=9; // Font size menu items
    }
    if (empty($vars['fontbold'])) {
        $vars['fontbold']=1; // Bold menu items 1 or 0
    }
    if (empty($vars['fontitalic'])) {
        $vars['fontitalic']=0; // Italic menu items 1 or 0
    }
    if (empty($vars['menutextcentered'])) {
        $vars['menutextcentered']='left'; // Item text position 'left', 'center' or 'right'
    }
    if (empty($vars['menucentered'])) {
        $vars['menucentered']='left'; // Menu horizontal position 'left', 'center' or 'right'
    }
    if (empty($vars['menuvertcentered'])) {
        $vars['menuvertcentered']='top'; // Menu vertical position 'top', 'middle','bottom' or static
    }
    if (empty($vars['childoverlap'])) {
        $vars['childoverlap']=0; // horizontal overlap child/ parent
    }
    if ($vars['childoverlap'] < 0 || $vars['childoverlap'] > 1) {
        $vars['childoverlap'] = 0;
    }
    if (empty($vars['childverticaloverlap'])) {
        $vars['childverticaloverlap']=0; // vertical overlap child/ parent
    }
    if ($vars['childverticaloverlap'] < 0 || $vars['childverticaloverlap'] > 1) {
        $vars['childverticaloverlap'] = 0;
    }
    if (empty($vars['starttop'])) {
        $vars['starttop']=0; // Menu offset x coordinate
    }
    if (empty($vars['startleft'])) {
        $vars['startleft']=0; // Menu offset y coordinate
    }
    if (empty($vars['vercorrect'])) {
        $vars['vercorrect']=0; // Multiple frames y correction
    }
    if (empty($vars['horcorrect'])) {
        $vars['horcorrect']=0; // Multiple frames x correction
    }
    if (empty($vars['leftpadding'])) {
        $vars['leftpadding']=3; // Left padding
    }
    if (empty($vars['toppadding'])) {
        $vars['toppadding']=2; // Top padding
    }
    if (empty($vars['firstlinehorizontal'])) {
        $vars['firstlinehorizontal']=0; // SET TO 1 FOR HORIZONTAL MENU, 0 FOR VERTICAL
    }    
    if (empty($vars['targetloc'])) {
        $vars['targetloc']=''; // span id for relative positioning
    }      
    if (empty($vars['disappeardelay'])) {
        $vars['disappeardelay']=1000; // delay before menu folds in
    }    
    if (empty($vars['unfoldsonclick'])) {
        $vars['unfoldsonclick']=0; // Level 1 unfolds onclick/ onmouseover
    }
    if (empty($vars['webmastercheck'])) {
        $vars['webmastercheck']=0; // menu tree checking on or off 1 or 0
    }
    if (empty($vars['keephilite'])) {
        $vars['keephilite']=0; // Keep selected path highligthed
    }
    if (empty($vars['showarrow'])) {
        $vars['showarrow']=0; // Use arrow gifs when 1
    }
    if (empty($vars['arrowgif']['name'])) {
        $vars['arrowgif']['name']='tri.gif'; // Arrow gif
        $vars['arrowgif']['width']=5; // Arrow gif width
        $vars['arrowgif']['height']=10; // Arrow gif height
    }    
    if (empty($vars['downarrowgif']['name'])) {
        $vars['downarrowgif']['name']='tridown.gif'; // Down arrow gif
        $vars['downarrowgif']['width']=5; // Down arrow gif width
        $vars['downarrowgif']['height']=10; // Down arrow gif height
    }
    if (empty($vars['leftarrowgif']['name'])) {
        $vars['leftarrowgif']['name']='trileft.gif'; // Left arrow gif
        $vars['leftarrowgif']['width']=5; // Left arrow gif width
        $vars['leftarrowgif']['height']=10; // Left arrow gif height
    }

    
    // Prepare output array
    if (!empty($vars['menu'])) {
        $menulines = explode("LINESPLIT", $vars['menu']);
        $vars['menulines'] = array();
        foreach ($menulines as $menuline) {
            $link = explode('|', $menuline);
            $vars['menulines'][] = $link; 
        }
    }
    
    return xarTplBlock('chsfnav', 'chsfmenunavadmin', $vars);
}

/**
 * update block settings
 *
 * @access  public
 * @param   $blockinfo
 * @return  $blockinfo data array
 * @throws  no exceptions
 * @todo    nothing
*/
function chsfnav_chsfmenunavblock_update($blockinfo)
{
    xarVarFetch('menumaxheight',        'isset', $vars['menumaxheight'],20);
    xarVarFetch('menumaxwidth',         'isset', $vars['menumaxwidth'],150);
    xarVarFetch('lowbgcolor',           'isset', $vars['lowbgcolor'],'#FFFFFF');
    xarVarFetch('lowsubbgcolor',        'isset', $vars['lowsubbgcolor'],'#FFFFFF');
    xarVarFetch('highbgcolor',          'isset', $vars['highbgcolor'],'#000000');
    xarVarFetch('highsubbgcolor',       'isset', $vars['highsubbgcolor'],'#000000');
    xarVarFetch('fontlowcolor',         'isset', $vars['fontlowcolor'],'#000000');
    xarVarFetch('fontsublowcolor',      'isset', $vars['fontsublowcolor'],'#000000');
    xarVarFetch('fonthighcolor',        'isset', $vars['fonthighcolor'],'#FFFFFF');
    xarVarFetch('fontsubhighcolor',     'isset', $vars['fontsubhighcolor'],'#FFFFFF');
    xarVarFetch('bordercolor',          'isset', $vars['bordercolor'],'#000000');
    xarVarFetch('bordersubcolor',       'isset', $vars['bordersubcolor'],'#000000');
    xarVarFetch('borderwidth',          'isset', $vars['borderwidth'],0);
    xarVarFetch('borderbtwnelmnts',     'isset', $vars['borderbtwnelmnts'],0);
    xarVarFetch('fontfamily',           'isset', $vars['fontfamily'],'arial,comic sans ms,technical'); //'Verdana'
    xarVarFetch('fontsize',             'isset', $vars['fontsize'],9);
    xarVarFetch('fontbold',             'isset', $vars['fontbold'],0);
    xarVarFetch('fontitalic',           'isset', $vars['fontitalic'],0);
    xarVarFetch('menutextcentered',     'isset', $vars['menutextcentered'],'left');
    xarVarFetch('menucentered',         'isset', $vars['menucentered'],'left');
    xarVarFetch('menuvertcentered',     'isset', $vars['menuvertcentered'],'top');
    xarVarFetch('childoverlap',         'isset', $vars['childoverlap'],0);
    xarVarFetch('childverticaloverlap', 'isset', $vars['childverticaloverlap'],0);
    xarVarFetch('starttop',             'isset', $vars['starttop'],0);
    xarVarFetch('startleft',            'isset', $vars['startleft'],0);
    xarVarFetch('vercorrect',           'isset', $vars['vercorrect'],0);
    xarVarFetch('horcorrect',           'isset', $vars['horcorrect'],0);
    xarVarFetch('leftpadding',          'isset', $vars['leftpadding'],3);
    xarVarFetch('toppadding',           'isset', $vars['toppadding'],2);     
    xarVarFetch('targetloc',            'isset', $vars['targetloc'],'');
    xarVarFetch('disappeardelay',       'isset', $vars['disappeardelay'],1000);
    xarVarFetch('firstlinehorizontal',  'isset', $vars['firstlinehorizontal'],0);  // 1 - horizontal, 0 - vertical
    xarVarFetch('unfoldsonclick',       'isset', $vars['unfoldsonclick'],0);
    xarVarFetch('webmastercheck',       'isset', $vars['webmastercheck'],0);
    xarVarFetch('showarrow',            'isset', $vars['showarrow'],0);
    xarVarFetch('keephilite',           'isset', $vars['keephilite'],0);    
    
    xarVarFetch('arrowgifname',         'isset', $vars['arrowgif']['name'],'tri.gif');
    xarVarFetch('arrowgifwidth',        'isset', $vars['arrowgif']['width'],5);
    xarVarFetch('arrowgifheight',       'isset', $vars['arrowgif']['height'],10);
    xarVarFetch('downarrowgifname',     'isset', $vars['downarrowgif']['name'],'tri.gif');
    xarVarFetch('downarrowgifwidth',    'isset', $vars['downarrowgif']['width'],5);
    xarVarFetch('downarrowgifheight',   'isset', $vars['downarrowgif']['height'],10);
    xarVarFetch('leftarrowgifname',     'isset', $vars['leftarrowgif']['name'],'tri.gif');
    xarVarFetch('leftarrowgifwidth',    'isset', $vars['leftarrowgif']['width'],5);
    xarVarFetch('leftarrowgifheight',   'isset', $vars['leftarrowgif']['height'],10);  

    $param_cache = xarCoreGetVarDirPath() . "/cache/chsfmenunav_par_$blockinfo[bid].js";
    $cache = xarCoreGetVarDirPath() . "/cache/chsfmenunav_$blockinfo[bid].js";

    // Write parameter cache file
    if( file_exists( $param_cache )) {
        unlink( $param_cache );
    }

    if( $fh = @fopen( $param_cache, "w" )) {
        // Dump the settings from $vars
        fwrite( $fh, "var LowBgColor=\"$vars[lowbgcolor]\";\n" );
        fwrite( $fh, "var LowSubBgColor=\"$vars[lowsubbgcolor]\";\n" );
        fwrite( $fh, "var HighBgColor=\"$vars[highbgcolor]\";\n" );
        fwrite( $fh, "var HighSubBgColor=\"$vars[highsubbgcolor]\";\n" );
        fwrite( $fh, "var FontLowColor=\"$vars[fontlowcolor]\";\n" );
        fwrite( $fh, "var FontSubLowColor=\"$vars[fontsublowcolor]\";\n" );
        fwrite( $fh, "var FontHighColor=\"$vars[fonthighcolor]\";\n" );
        fwrite( $fh, "var FontSubHighColor=\"$vars[fontsubhighcolor]\";\n" );
        fwrite( $fh, "var BorderColor=\"$vars[bordercolor]\";\n" );
        fwrite( $fh, "var BorderSubColor=\"$vars[bordersubcolor]\";\n" );
        fwrite( $fh, "var BorderWidth=$vars[borderwidth];\n" );
        fwrite( $fh, "var BorderBtwnElmnts=$vars[borderbtwnelmnts];\n" );
        fwrite( $fh, "var FontFamily=\"$vars[fontfamily]\";\n" );
        fwrite( $fh, "var FontSize=$vars[fontsize];\n" );
        fwrite( $fh, "var FontBold=$vars[fontbold];\n" );
        fwrite( $fh, "var FontItalic=$vars[fontitalic];\n" );
        fwrite( $fh, "var MenuTextCentered=\"$vars[menutextcentered]\";\n" );
        fwrite( $fh, "var MenuCentered=\"$vars[menucentered]\";\n" );
        fwrite( $fh, "var MenuVerticalCentered=\"$vars[menuvertcentered]\";\n" );
        fwrite( $fh, "var ChildOverlap=$vars[childoverlap];\n" );
        fwrite( $fh, "var ChildVerticalOverlap=$vars[childverticaloverlap];\n" );
        fwrite( $fh, "var StartTop=$vars[starttop];\n" );
        fwrite( $fh, "var StartLeft=$vars[startleft];\n" );
        fwrite( $fh, "var VerCorrect=$vars[vercorrect];\n" );
        fwrite( $fh, "var HorCorrect=$vars[horcorrect];\n" );
        fwrite( $fh, "var LeftPaddng=$vars[leftpadding];\n" ); 
        fwrite( $fh, "var TopPaddng=$vars[toppadding];\n" );               
        if( trim($vars['targetloc']) == '' ) {
            $vars['targetloc'] = "chsfmenunav_$blockinfo[bid]";
        }
        fwrite( $fh, "var TargetLoc=\"$vars[targetloc]\";\n" );        
        fwrite( $fh, "var DissapearDelay=$vars[disappeardelay];\n" );
        fwrite( $fh, "var TakeOverBgColor=1;\n" );            
        fwrite( $fh, "var FirstLineHorizontal=$vars[firstlinehorizontal];\n" );       
        fwrite( $fh, "var MenuFramesVertical=0;\n" );  // Ignoring frames in menu        
        fwrite( $fh, "var FirstLineFrame=\"\";\n" ); // Ignoring frames in menu
        fwrite( $fh, "var SecLineFrame=\"\";\n" ); // Ignoring frames in menu
        fwrite( $fh, "var DocTargetFrame=\"\";\n" ); // Ignoring frames in menu
        fwrite( $fh, "var HideTop=$vars[firstlinehorizontal];\n" );
        fwrite( $fh, "var MenuWrap=$vars[firstlinehorizontal];\n" );
        fwrite( $fh, "var RightToLeft=$vars[firstlinehorizontal];\n" );            
        fwrite( $fh, "var UnfoldsOnClick=$vars[unfoldsonclick];\n" );
        fwrite( $fh, "var WebMasterCheck=$vars[webmastercheck];\n" );
        fwrite( $fh, "var ShowArrow=$vars[showarrow];\n" );
        fwrite( $fh, "var KeepHilite=$vars[keephilite];\n" );
        fwrite( $fh, "var Arrws=['".$vars['arrowgif']['name']."',".$vars['arrowgif']['width'].",".$vars['arrowgif']['height'].",'".$vars['downarrowgif']['name']."',".$vars['downarrowgif']['width'].",".$vars['downarrowgif']['height'].",'".$vars['leftarrowgif']['name']."',".$vars['leftarrowgif']['width'].",".$vars['leftarrowgif']['height']."];\n" );
        @fclose( $fh );
    }
    
    // User links
    if (!xarVarFetch('linkurl', 'array:1:', $linkurl, array())) {return;}
    if (!xarVarFetch('linkname', 'array:1:', $linkname, array())) {return;}
    if (!xarVarFetch('linkdesc', 'array:1:', $linkdesc, array())) {return;}
    if (!xarVarFetch('linkdelete', 'array:1:', $linkdelete, array())) {return;}
    if (!xarVarFetch('linkchild', 'array:1:', $linkchild, array())) {return;}

    $menu = array();
    for ($idx = 0; $idx < count($linkname); $idx++) {
        if (!isset($linkdelete[$idx])) {
            @$menu[] = "$linkurl[$idx]|$linkname[$idx]|$linkdesc[$idx]|$linkchild[$idx]";
        }
    } 

    // Get new menu
    xarVarFetch('new_linkurl',   'str:1:', $new_linkurl,   '');
    xarVarFetch('new_linkname',  'str:1:', $new_linkname,  '');
    xarVarFetch('new_linkdesc',  'str:1:', $new_linkdesc,  '');
    xarVarFetch('new_linkchild', 'int:0:1:', $new_linkchild, '');

    if (!empty($new_linkname)) {
       $menu[] = $new_linkurl.'|'.$new_linkname.'|'.$new_linkdesc.'|'.$new_linkchild;
    }
    
    $vars['menu'] = implode("LINESPLIT", $menu);
    $blockinfo['content']= serialize($vars);

    // Write menu to cache file 
    if (!empty($vars['menu'])) {
        $usermenu = array();
        $menulines = $menu;
        foreach ($menulines as $menuline) {
            $parts = explode('|', $menuline);
            $url = $parts[0];
            $title = $parts[1];
            $comment = $parts[2];
            $child = isset($parts[3]) ? $parts[3] : '';

            $title = xarVarPrepForDisplay($title);
            $url = xarVarPrepForDisplay($url);
            $comment = xarVarPrepForDisplay($comment);
            $child = xarVarPrepForDisplay($child);
            $usermenu[] = array('title' => $title, 'url' => $url, 'comment' => $comment, 'child'=> $child, 'children'=>array());
        } // foreach
                
        //build 2level menu tree
        $parent_id=0;
        $top_menus=0;
        $tree = array();
        
        foreach($usermenu as $id => $item ) {
            if(!empty($item['child']) && isset($usermenu[$parent_id])) {
                array_push( $usermenu[$parent_id]['children'], $id );
                $tree[$id] = $tree[$parent_id]."_".count( $usermenu[$parent_id]['children'] );
            } else {
                $top_menus++;
                $parent_id=$id;
                $tree[$id]=$top_menus;
            }
        }

        if ($vars['firstlinehorizontal'] == 0 ) { 
            // Vertical
            xarModSetVar('chsfnav', 'jmbheight', $parent_id);
            xarModSetVar('chsfnav', 'jmbwidth', 1);
        } else { 
            // Horizontal
            xarModSetVar('chsfnav', 'jmbheight', 1);
            xarModSetVar('chsfnav', 'jmbwidth', $parent_id);
        }

        if( file_exists( $cache )) {
            unlink( $cache );
        }
        
        if( $fh = @fopen( $cache, "w" )) {
            fwrite( $fh, "var NoOffFirstLineMenus=$top_menus;\n" );
            
            // Dump the menu items from $menus
            foreach( $usermenu as $id => $menuitem ) {
                fwrite( $fh, "Menu$tree[$id]=new Array(\"$menuitem[title]\",\"$menuitem[url]\",\"\",".count($menuitem['children']).", $vars[menumaxheight], $vars[menumaxwidth],\"\",\"\",\"\",\"\",\"\");\n" );
            }
            
            fwrite( $fh, "function BeforeStart(){return}\n" );
            fwrite( $fh, "function AfterBuild(){return}\n" );
            fwrite( $fh, "function BeforeFirstOpen(){return}\n" );
            fwrite( $fh, "function AfterCloseAll(){return}\n" );
            @fclose( $fh );
        }                
    }

    // Ensure we have a title for the block.
    if (empty($blockinfo['title'])){
        $blockinfo['title'] = xarML('Main Menu');
    }

    return($blockinfo);
}

?>

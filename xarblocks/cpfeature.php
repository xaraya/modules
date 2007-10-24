<?php
/**
 * File:
 *
 * xarCPShop Feature Block
 *
 * @copyright (C) 2004 by Jo Dalle Nogare
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage xarCPShop
 * @author jojodee@xaraya.com
 */

/**
 * @author jojodee
 * @original author of file Ed Grosvener
 * @TODO: This really has to be cleaned up from the original port to Xaraya
 *       Redo and bring into line with todo changes in 'main' display function
 *       initialise block
 */
function xarcpshop_cpfeatureblock_init()
{
     return array(
        'numitems' => 1,
        'featuredstore' =>(int)xarModGetVar('xarcpshop','defaultstore')
    );
} 

/**
 * get information on block
 */
function xarcpshop_cpfeatureblock_info()
{ 
    // Values
    return array('text_type' => 'Featured',
        'module' => 'xarcpshop',
        'text_type_long' => 'Display featured product',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true);
}

/**
 * display block
 */
function xarcpshop_cpfeatureblock_display($blockinfo)
{
    // Security check
    if (!xarSecurityCheck('ReadxarCPShopBlock', 0, 'Block', $blockinfo['title'])) {return;}

     if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    // Defaults
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 1;
    }
    if (empty($vars['featuredstore']) || (!isset($vars['featuredstore']))) {
        $vars['featuredstore'] = (int)xarModGetVar('xarcpshop','defaulstore');
    }

    $storeid=$vars['featuredstore'];

       $items = xarModAPIFunc('xarcpshop',
                             'user',
                             'get',
                             array('storeid' => $vars['featuredstore']));
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) {return;} // throw back




    //set var for shorturl use
    if ((xarModGetVar('xarcpshop','SupportShortURLs')) &&
        (xarConfigGetVar('Site.Core.EnableShortURLsSupport'))){

        $useshorturls=1;
    } else {
        $useshorturls=0;
    }
  // Display each item, permissions permitting
    if (!isset($cp) && $items['toplevel'] != '' )   {
        $cp = $items['name'] ."/" .$items['toplevel'];
    }
    if (!isset($cp)){
        $cp= $items['name'];
    }

    $data['items'] = array();
    $itemsacross = 3;
    $i = 1;
    $cpitems = count($items);
    $additems = array();
    
    $storeasblock = 0;
    
    if($storeasblock == 1)   {
        
        $content = xarModAPIFunc('xarcpshop',
                                    'user',
                                    'getpage',
                             array('cp' => $cp));

        if (!isset($content) && xarCurrentErrorType() != XAR_NO_EXCEPTION) {return;} // throw back

        $content = $content[0];

        $js = "<script>\n<!--\n
                function e (z, h, w, b, g) 
                {\n
                document.write('<div style=\"width:'+w+';height:'+h+';background:white url(http://zoom.cafepress.com/'+(z%10)+'/'+z+'_zoom.jpg) no-repeat center center;\"><img border=\"'+b+'\" class=\"imageborder\" src=\"http://www.cafepress.com/cp/img/'+(g?'zoom':'spacer')+'.gif\" width=\"'+w+'\" height=\"'+h+'\"></div>')\n
                }\n-->\n
                </script>\n
                <script language=\"JavaScript1.1\" src=\"http://www.cafepress.com/commonscripts.js\"></script>";
        $content = eregi_replace('"/cp/' , '"http://www.cafepress.com/cp/', $content);
        $content = eregi_replace('\'/cp/','\'http://www.cafepress.com/cp/',$content);
        if ($useshorturls ==1) {
            $content = eregi_replace('<a href="/',  '<a href="index.php/xarcpshop/', $content);
        }else{
            $content = eregi_replace('<a href="/',  '<a href="index.php?module=xarcpshop&type=user&id=', $content);
        }
        if ($breadcrumb == 0)   {
            $content = eregi_replace('<p class="storesmallprint">','<p class=storesmallprint" style="visibility:hidden; font-size:0px;">', $content);
        }
        $content = eregi_replace('<form method="post" action="http://www.cafepress.com/cp/addtocart.aspx">', '<form method="post" name="cart" action="http://www.cafepress.com/cp/addtocart.aspx?keepshopping=javascript:self.close()" target="cartWin">', $content);
        $content = eregi_replace('<input type="submit"', "<input type=\"submit\" onclick=\"cartWin = window.open ('','cartWin','toolbar=yes,location=no,directories=no,status=yes,scrollbars=yes,resizable=yes,copyhistory=no,width=800,height=500'); cartWin.focus(); return true;\"", $content);
        if ($useshorturls ==1) {
            $content = eregi_replace('<a href="#top">', '<a href="index.php?/xarcpshop/'.$cp.'#top">', $content);
        }else{
            $content = eregi_replace('<a href="#top">', '<a href="index.php?module=xarcpshop&type=user&id='.$cp.'#top">', $content);
        }
        $content = eregi_replace('Â', '', $content);
        $content = eregi_replace('<img src="/', '<img src="http://www.cafepress.com/', $content);
        $content = eregi_replace("</head>" , '', $content);
        $content = eregi_replace("cellpadding=\"8\"", "cellpadding=\"4\" cellspacing=\"0\"", $content);
        $content = eregi_replace("<td align=\"center\" valign=\"top\">" , "<td align=\"center\" valign=\"top\" width=\"205\">", $content);
        $content = eregi_replace("width=\"100%\"", '', $content);
        $blockinfo['js']=$js;
        $blockinfo['blockstore']=$content;
        $data['items']=$item;

    }    else   {

        $content = xarModAPIFunc('xarcpshop',
                                 'user',
                                 'getcpparts',
                            array('cp'        => $cp,
                                  'getstring' => 'src="http://storetn'));

        if ($content == false) {
            return;
        }

        $itemsacross = 3;
        $i = 1;
        $cpitems = count($content);


           $numbers = xarModAPIFunc('xarcpshop',
                                       'user',
                                    'mrand',
                                    array('l'   => 0,
                                      'h'   => $cpitems,
                                      't'   => $vars['numitems'],
                                      'len' => false));

        $totalcontent=count($numbers);
        for ($i = 0; $i < $totalcontent; $i++) {
            if (isset($content[$i])){
              $content[$i] = eregi_replace('<tr>', '', $content[$i]);
            $content[$i] = eregi_replace('</tr>', '' , $content[$i]);
            $content[$i] = eregi_replace('<td>', '', $content[$i]);
            $content[$i] = eregi_replace('</td>', '', $content[$i]);
            $content[$i] = eregi_replace('"/cp/','"http://www.cafepress.com/cp/', $content[$i]);
            if ($useshorturls ==1) {
                $content[$i] = eregi_replace('<a href="/', '<a href="index.php/xarcpshop/', $content[$i]);
            }else{
                $content[$i] = eregi_replace('<a href="/', '<a href="index.php?module=xarcpshop&type=user&id=', $content[$i]);
            }
            $item['detail'] = $content[$i];
            }
        }
        $data['items'][]=$item;
    }

    $data['blockid'] = $blockinfo['bid'];

    // Now we need to send our output to the template.
    // Just return the template data.

    $blockinfo['storeasblock'] = $storeasblock;
    $blockinfo['content'] = $data;

    return $blockinfo;
} 

?>

<?php
/**
 * File:
 *
 * xarCPShop Random Block
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage CP SHop
 * @author jojodee@xaraya.com
 */

/**
 * initialise block
 */
function xarcpshop_cprandomblock_init()
{
    return array(
        'numitems' => 5,
       'featuredstore' =>(int)xarModGetVar('xarcpshop','defaultstore')

    );
} 

/**
 * get information on block
 */
function xarcpshop_cprandomblock_info()
{ 
    // Values
    return array('text_type' => 'Random',
        'module' => 'xarcpshop',
        'text_type_long' => 'Display random products',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true);
}

/**
 * display block
 */
function xarcpshop_cprandomblock_display($blockinfo)
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
        $vars['numitems'] = 5;
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
    if (empty($vars['numitems'])) $vars['numitems'] = $cpitems;
    if (!isset($content) || !is_array($content) || $cpitems == 0) {
            return;
    } else {
            if ($cpitems <= $vars['numitems']) $randomitem = array_rand($content, $cpitems);
                else $randomitem = array_rand($content, $vars['numitems']);
            if(!is_array($randomitem)) $randomitem = array($randomitem);

            foreach ($randomitem as $randomid) {
                if (!empty($content[$randomid]['authorid']) && !empty($vars['showauthor'])) {
                    if (empty($items[$randomid]['name'])) {
                        xarErrorHandled();
                        $items[$randomid]['name'] = xarML('Unknown');
                    }
                }
                $numbers[] = $content[$randomid];
            }

    }
        $totalcontent=count($numbers);
        for ($i = 0; $i < $totalcontent; $i++) {
            if (isset($numbers[$i])){
              $numbers[$i] = eregi_replace('<tr>', '', $numbers[$i]);
            $numbers[$i] = eregi_replace('</tr>', '' , $numbers[$i]);
            $numbers[$i] = eregi_replace('<td>', '', $numbers[$i]);
            $numbers[$i] = eregi_replace('</td>', '', $numbers[$i]);
            $numbers[$i] = eregi_replace('"/cp/','"http://www.cafepress.com/cp/', $numbers[$i]);
            if ($useshorturls ==1) {
                $numbers[$i] = eregi_replace('<a href="/', '<a href="index.php/xarcpshop/', $numbers[$i]);
            }else{
                $numbers[$i] = eregi_replace('<a href="/', '<a href="index.php?module=xarcpshop&type=user&id=', $numbers[$i]);
            }
            $item['detail'] = $numbers[$i];
            }
        }
        $data['items'][]=$item;
  

    $data['blockid'] = $blockinfo['bid'];

    // Now we need to send our output to the template.
    // Just return the template data.

   // $blockinfo['storeasblock'] = $storeasblock;
    $blockinfo['content'] = $data;

    return $blockinfo;
} 

?>

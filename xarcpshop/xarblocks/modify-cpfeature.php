<?php
/**
 * File: $Id:
 *
 * xarCPShop Feature Block
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCPShop
 * @author jojodee module development team
 */

/**
 * modify block settings
 */
function xarcpshop_cpfeatureblock_modify($blockinfo)
{
    // Get current content
    if (!is_array($blockinfo['content'])) {
        $vars = unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    // Defaults
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    }
    if (empty($vars['featuredstore']) || (!isset($vars['featuredstore']))) {
        $vars['featuredstore']=(int)xarModGetVar('xarcpshop','defaultstore');
    }

    $shops = xarModAPIFunc('xarcpshop',
                                 'user',
                                 'getall');
    $allshops =count($shops);
    $shopdata=array();
    for ($i = 0; $i < $allshops; $i++) {
       $shopdata[$i]['id']=(int)$shops[$i]['storeid'];
       $shopdata[$i]['name']=$shops[$i]['name'];
       //$shopitem[]=$shopdata;
    }
    // Send content to template
    return array('numitems' => $vars['numitems'],
                 'featuredstore' => $vars['featuredstore'],
                 'shopdata'=>$shopdata,
                 'blockid' => $blockinfo['bid']);
}

/**
 * update block settings
 */
function xarcpshop_cpfeatureblock_update($blockinfo)
{
    if (!xarVarFetch('numitems', 'int:1:', $vars['numitems'], 1, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('featuredstore', 'int:1:', $vars['featuredstore'], 1, XARVAR_DONT_SET)) {return;}
    $blockinfo['content'] = $vars;

    return $blockinfo;
}

?>

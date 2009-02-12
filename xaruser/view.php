<?php
/**
 * Dossier Module - A Contact and Customer Service Management Module
 *
 * @package modules
 * @copyright (C) 2002-2007 Chad Kraeft
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dossier Module
 * @link http://xaraya.com/index.php/release/829.html
 * @author Chad Kraeft
 */
function dossier_user_view($args)
{
    extract($args);
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ltr', 'str::', $ltr, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sortby', 'str::', $sortby, 'sortcompany', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('private', 'str::', $private, "off", XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cat_id', 'str::', $cat_id, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('q', 'str::', $q, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('searchphone', 'str::', $searchphone, '', XARVAR_NOT_REQUIRED)) return;
            
    if (!xarSecurityCheck('PublicDossierAccess', 0, 'Contact', "All:All:All:All")) {//TODO: security
        $msg = xarML('Not authorized to access #(1) items',
                    'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    $data = xarModAPIFunc('dossier', 'user', 'menu');
    
    $data['ltr'] = $ltr;
    $data['sortby'] = $sortby;
    $data['startnum'] = $startnum;
    $data['private'] = $private ? $private : "off";
    $data['cat_id'] = $cat_id;
    $data['q'] = $q;

    $contactlist = xarModAPIFunc('dossier', 'user', 'getall',
                            array('ltr' => $ltr,
                                  'sortby' => $sortby,
                                  'private' => $private,
                                  'cat_id' => $cat_id,
                                  'q' => $q,
                                  'searchphone' => $searchphone,
                                  'startnum' => $startnum,
                                  'numitems' => xarModGetVar('dossier','itemsperpage')));
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    
    $data['contactlist'] = $contactlist;
    
    $uid = xarUserGetVar('uid');
    
    // need to flag if is first page, last page, or in between
    // first page: startnum = 0/1/empty
    // last page: startnum + itemsperpage >= countitems
    // else in between
    $itemsperpage = xarModGetUserVar('dossier', 'itemsperpage', $uid);
    $data['itemsperpage'] = $itemsperpage;
    $ttlitems = xarModAPIFunc('dossier', 
                            'user', 
                            'countitems', 
                            array('ltr' => $ltr,
                                  'sortby' => $sortby,
                                  'private' => $private,
                                  'cat_id' => $cat_id,
                                  'q' => $q));
                                  
    $data['itemsperpage'] = $itemsperpage;
    $data['ttlitems'] = $ttlitems;
    if($itemsperpage < $ttlitems) {
        if($startnum <= 1) {
            $data['pagetype'] = "first";
        } elseif(($startnum + $itemsperpage) > $ttlitems) {
            $data['pagetype'] = "last";
        } else {
            $data['pagetype'] = "mid";
        }
    } else {
        $data['pagetype'] = "only";
    }
        
    $data['pager'] = xarTplGetPager($startnum,
                                    $ttlitems,
                                    xarModURL('dossier', 'admin', 'view', array('startnum' => '%%')),
                                    $itemsperpage);
        
	return $data;
}

?>

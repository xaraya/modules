<?php
/**
 * XTask Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XTask Module
 * @link http://xaraya.com/index.php/release/704.html
 * @author St.Ego
 */
function xtasks_user_completed($args)
{
    extract($args);
    
    if (!xarVarFetch('startnum',   'int:1:', $startnum,   1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('mymemberid',   'int', $mymemberid,   0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('memberid',   'int', $memberid,   0, XARVAR_NOT_REQUIRED)) return;
    
    $data = xarModAPIFunc('xtasks','admin','menu');

    if (!xarSecurityCheck('ViewXTask')) {
        return;
    }

    $xtasks = xarModAPIFunc('xtasks',
                          'user',
                          'getall',
                          array('mymemberid' => $mymemberid,
                                'memberid' => $memberid,
                                'statusfilter' => "Closed",
                                'numitems' => 20));//TODO: numitems

    if (!isset($xtasks) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    $data['xtasks'] = $xtasks;
    
    return $data;
}

?>

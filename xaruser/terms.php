<?php
/**
 * Shows the user terms if set as a modvar
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Registration module
 * @link http://xaraya.com/index.php/release/30205.html
 */
/**
 * Shows the user terms if set as a modvar
 * @author  Marc Lutolf <marcinmilan@xaraya.com>
 * @return array empty array
 */
function registration_user_terms()
{
    // Security check
    if (!xarSecurityCheck('ViewRegistration')) return;
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Terms of Usage')));
    $data['link'] = '';
    
    /*$link = xarModVars::get('registration','termslink');
    if (!empty($link)) {
        $url_parts = parse_url($link);
        if (!isset($url_parts['host'])) {
            $truecurrenturl = xarServerGetCurrentURL(array(), false);
            $urldata = xarModAPIFunc('roles','user','parseuserhome',array('url'=>$link,'truecurrenturl'=>$truecurrenturl));
            $link = $urldata['redirecturl'];
        }
        $link = parse_url($link);
        var_dump($link);exit;
        $data['link']    = (!empty($link) && $link != 'http://') ? $link : '';
    }
    */
    return $data;
}
?>
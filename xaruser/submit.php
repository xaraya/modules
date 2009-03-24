<?php
/**
 * Handles user submissions
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Netquery Module
 * @link http://xaraya.com/index.php/release/230.html
 */

function netquery_user_submit()
{
    $data['stylesheet'] = xarModGetVar('netquery', 'stylesheet');
    if (!xarSecurityCheck('ReadNetquery',0)) return;
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'form', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('Submit', 'str:1:100', $Submit, 'Cancel', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('portnum', 'int:1:100000', $portnum, '80', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    switch(strtolower($phase))
    {
        case 'form':
        default:
            $data['authid']         = xarSecGenAuthKey();
            $data['portnum']        = $portnum;
            $data['submitlabel']    = xarML('Submit');
            $data['cancellabel']    = xarML('Cancel');
            break;
        case 'update':
            if ((!isset($Submit)) || ($Submit != xarML('Submit')))
            {
                xarResponseRedirect(xarModURL('netquery', 'user', 'main'));
            }
            if (!xarVarFetch('port_port', 'int:1:100000', $port_port)) return;
            if (!xarVarFetch('port_protocol', 'str:1:3', $port_protocol)) return;
            if (!xarVarFetch('port_service', 'str:1:35', $port_service)) return;
            if (!xarVarFetch('port_comment', 'str:1:50', $port_comment, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('port_flag', 'int:1:100000', $port_flag, 99, XARVAR_NOT_REQUIRED)) return;
            if (!xarSecConfirmAuthKey()) return;
            if (!xarModAPIFunc('netquery', 'user', 'ptsubmit',
                               array('port_port'     => $port_port,
                                     'port_protocol' => $port_protocol,
                                     'port_service'  => $port_service,
                                     'port_comment'  => $port_comment,
                                     'port_flag'     => $port_flag))) return;
            xarResponseRedirect(xarModURL('netquery', 'user', 'submit', array('phase' => 'thanks')));
            break;
        case 'thanks':
            $returl = xarModURL('netquery', 'user', 'main');
            $data['thankyou']  = '<h2 align="center">' . xarML('Thank You') . '</a></h2><p>';
            $data['thankyou'] .= xarML('Your submission has been processed for the administrator\'s attention. Upon approval, it will be visible in the services and exploits listing for the port specified.');
            $data['thankyou'] .= '</p><p>';
            $data['thankyou'] .= xarML('To return to the Netquery user interface please ');
            $data['thankyou'] .= '<a href="'.$returl.'">' . xarML('Click here') . '</a> </p>';
            break;
    }
    return $data;
}
?>
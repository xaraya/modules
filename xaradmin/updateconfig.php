<?php
/**
 * Polls module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Polls Module
 * @link http://xaraya.com/index.php/release/23.html
 * @author Jim McDonalds, dracos, mikespub et al.
 */
/**
 * Update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function polls_admin_updateconfig()
{
    // Get parameters

    if (!xarVarFetch('barscale', 'str:1:', $barscale, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('defaultopts', 'str:1:', $defaultopts, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('imggraph', 'str:0:3', $imggraph, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('voteinterval', 'str:1:', $voteinterval, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('previewresults', 'str:1:', $previewresults, 'single', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showtotalvotes', 'int', $showtotalvotes, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shorturl', 'str:1:', $shorturl, 0, XARVAR_NOT_REQUIRED)) return;

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    // Security Check
    if (!xarSecurityCheck('AdminPolls')) {
        return;
    }

    // Check arguments
    if (!is_numeric($barscale) || $barscale <= 0) {
        $msg = xarML("Invalid value for config variable: barscale");
        throw new BadParameterException($barscale,$msg);
    }

    if (strval(intval($defaultopts)) !== $defaultopts || $defaultopts < 2) {
        $msg = xarML("Invalid value for config variable: defaultopts");
        throw new BadParameterException($defaultopts,$msg);
    }

    if (strval(intval($imggraph)) !== $imggraph || $imggraph < 0 || $imggraph > 3) {
        $msg = xarML("Invalid value for config variable: imggraph");
        throw new BadParameterException($imggraph,$msg);
    }
    
    if (!(($voteinterval == -1) ||
        ($voteinterval == 86400) ||
        ($voteinterval == 604800) ||
        ($voteinterval == 2592000))) {
        
        $msg = xarML("Invalid value for config variable: voteinterval");
        throw new BadParameterException($voteinterval,$msg);
    }
    if ($previewresults != 1) {
        $previewresults = 0;
    }

    // update the data

    xarModVars::set('polls', 'barscale', $barscale);
    xarModVars::set('polls', 'defaultopts', $defaultopts);
    xarModVars::set('polls', 'imggraph', $imggraph);
    xarModVars::set('polls', 'voteinterval', $voteinterval);
    xarModVars::set('polls', 'previewresults', $previewresults);
    xarModVars::set('polls', 'showtotalvotes', $showtotalvotes);
    xarModVars::set('polls', 'SupportShortURLs', $shorturl);

    xarModCallHooks('module','updateconfig','polls',
                    array('module' => 'polls'));

    xarResponseRedirect(xarModURL('polls', 'admin', 'modifyconfig'));

    // Return
    return true;
}

?>

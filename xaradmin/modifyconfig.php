<?php
/**
 * Headlines - Generates a list of feeds
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @link http://www.xaraya.com/index.php/release/777.html
 * @author John Cox
 */
function headlines_admin_modifyconfig()
{
    if(!xarSecurityCheck('AdminHeadlines')) return;
    $hooks = xarModCallHooks('module', 'modifyconfig', 'headlines', array('module' => 'headlines'));
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    $data['shorturlslabel']     = xarML('Enable short URLs?');
    $data['shorturlschecked']   = xarModGetVar('headlines', 'SupportShortURLs') ? true : false;
    $data['modulealias'] = xarModGetVar('headlines', 'useModuleAlias');
    $data['aliasname'] = xarModGetVar('headlines', 'aliasname');
    $data['showcomments'] = xarModGetVar('headlines', 'showcomments');
    if (!xarModIsAvailable('comments') || !xarModIsHooked('comments', 'headlines')) {
        $data['showcomments'] = 0;
    }
    $data['showratings'] = xarModGetVar('headlines', 'showratings');
    if (!xarModIsAvailable('ratings') || !xarModIsHooked('ratings', 'headlines')) {
        $data['showratings'] = 0;
    }
    $data['showhitcount'] = xarModGetVar('headlines', 'showhitcount');
    if (!xarModIsAvailable('hitcount') || !xarModIsHooked('hitcount', 'headlines')) {
        $data['showhitcount'] = 0;
    }
    $data['showkeywords'] = xarModGetVar('headlines', 'showkeywords');
    if (!xarModIsAvailable('keywords') || !xarModIsHooked('keywords', 'headlines')) {
        $data['showkeywords'] = 0;
    }
    $data['maxdescription'] = xarModGetVar('headlines', 'maxdescription');
    // Magpie modvar deprecated
    $data['magpiechecked']      = xarModGetVar('headlines', 'magpie') ? true : false;

    $data['parser']             = xarModGetVar('headlines', 'parser');
    if (empty($data['parser'])) $data['parser'] = ($data['magpiechecked'] ? 'magpie' : 'default');

    /* TODO: present per parser options to show whatever each parser presents as data
    /* is this the best way? check existing parser module options (simplepie, magpie)
    /* do we want to provide per feed parsing otions in the future? 
    switch ($data['parser']) {
        case 'default':
            default:
                // alt title, description, link, truncate, show link, TODO: what else? php5 XML parser?
            break;
        case 'magpie':
                // TODO: evaluate data returned by magpie, images, etc.
            break;
        case 'simplepie':
                // TODO: evaluate data returned by simplepie, images, cats, etc.

            break;
    }
    */

    $data['authid']             = xarSecGenAuthKey();
    $data['pubtypes']           = xarModAPIFunc('articles', 'user', 'getpubtypes');
    $data['importpubtype']      = xarModGetVar('headlines', 'importpubtype');
    return $data;
}
?>

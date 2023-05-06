<?php
/**
 * Pubsub Module
 *
 * @package modules
 * @subpackage pubsub module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Add Subscription
 * @param sub_module
 * @param sub_itemtype
 * @param sub_category
 * @param sub_email
 * @param sub_htmlmail
 * @return bool true
 */
function pubsub_admin_addsubscriber()
{
    // Get parameters
    xarVar::fetch('sub_module','isset',$sub_module,'', xarVar::DONT_SET);
    xarVar::fetch('sub_itemtype','isset',$sub_itemtype,'', xarVar::DONT_SET);
    xarVar::fetch('sub_category','isset',$sub_category,'', xarVar::DONT_SET);
    xarVar::fetch('sub_email','isset',$sub_email,'', xarVar::DONT_SET);
    xarVar::fetch('sub_htmlmail','checkbox',$sub_htmlmail,true, xarVar::DONT_SET);
    // Confirm authorisation code
//    if (!xarSec::confirmAuthKey()) return;
    // Security Check
    if (!xarSecurity::check('AdminPubSub')) return;

    $sub_args = array();
    $sub_args['modid']    = $sub_module;
    $sub_args['cid']      = $sub_category;
    $sub_args['itemtype'] = $sub_itemtype;
    $sub_args['actionid'] = $sub_htmlmail ? 2:1 ;

    if( strstr($sub_email,"\n") )
    {
        $emails = explode("\n",$sub_email);
        foreach( $emails as $email )
        {
            $sub_args['email']    = $email;
            xarMod::apiFunc('pubsub','user','subscribe', $sub_args);
        }
    } else {
        $sub_args['email']    = $sub_email;
        xarMod::apiFunc('pubsub','user','subscribe', $sub_args);
    }


    xarController::redirect(xarController::URL('pubsub', 'admin', 'viewall'));

    return true;
}

?>

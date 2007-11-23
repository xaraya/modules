<?php
/**
 * Pubsub module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Pubsub Module
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 */
/**
 * Add Subscriber
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
    xarVarFetch('sub_module','isset',$sub_module,'', XARVAR_DONT_SET);
    xarVarFetch('sub_itemtype','isset',$sub_itemtype,'', XARVAR_DONT_SET);
    xarVarFetch('sub_category','isset',$sub_category,'', XARVAR_DONT_SET);
    xarVarFetch('sub_email','isset',$sub_email,'', XARVAR_DONT_SET);
    xarVarFetch('sub_htmlmail','checkbox',$sub_htmlmail,true, XARVAR_DONT_SET);
    // Confirm authorisation code
//    if (!xarSecConfirmAuthKey()) return;
    // Security Check
    if (!xarSecurityCheck('AdminPubSub')) return;

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
            xarModAPIFunc('pubsub','user','subscribe', $sub_args);
        }
    } else {
        $sub_args['email']    = $sub_email;
        xarModAPIFunc('pubsub','user','subscribe', $sub_args);
    }


    xarResponseRedirect(xarModURL('pubsub', 'admin', 'viewall'));

    return true;
}

?>

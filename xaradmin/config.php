<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */
/**
 * Administration for the mybookmarks module.
 */
function messages_admin_config( $args )
{
    extract( $args );
    if (!xarVarFetch('cancel',    'str:1:', $cancel, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemtype',  'int',    $itemtype, 0, XARVAR_NOT_REQUIRED)) return;
    // check if the user selected cancel
    if ( !empty( $cancel ) ) {

        // This function generated no output, and so now it is complete we redirect
        // the user to an appropriate page for them to carry on their work
        xarResponseRedirect(
            xarModURL('messages','admin','config',array('itemtype' => $itemtype )));
        }

    switch( $itemtype ) {
        case 1:
            return xarModAPIFunc('messages','messages','config',$args );

        default:
            return messages_adminpriv_config( $args );
    }
}

/**
 * Administration for the mybookmarks module.
 */
function messages_adminpriv_config( $args )
{
    extract( $args );
    if (!xarVarFetch('itemtype',  'int',    $itemtype, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shorturls', 'int:0:1', $shorturls, 0, XARVAR_NOT_REQUIRED)) return;
    //Psspl:Modifided the code for allowedsend to selected group configuration.
    if (!xarVarFetch('selectedGroups',  'array',    $selectedGroups, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('action',    'str:1:', $action, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('group',  'int',    $group, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('childgroupsimploded',  'str',    $childgroupsimploded, 0, XARVAR_NOT_REQUIRED)) return;   
       
    $data['group'] = $group;
    
    if ( isset( $authid ) ) {
        /* The user confirmed the form. So save the results.         */

        if (!xarSecConfirmAuthKey()) return;

        if ( empty( $supportshorturls ) or !is_numeric( $supportshorturls ) ) {
            $supportshorturls = 0;
        }

        xarModVars::set('messages','SupportShortURLs',$supportshorturls );

        /*
         * Finished. Back to the sender!
         */
        xarResponseRedirect(
            xarModURL('messages','admin','config', array('itemtype' => $itemtype )));

    } // Save the changes
    //Psspl:Added the code for modify action for storing selected information.
   if ($action == 'Modify') {
                xarModAPIFunc('messages','admin','setconfig',array('group'=>$data['group'],'childgroupsimploded' => $childgroupsimploded));
        
    }
    $data['selectedGroupStr'] = xarModAPIFunc('messages','admin','getconfig',array('group'=>$data['group']));
    $data['common']['menu_label'] = 'Configure';
    $data['common']['menu']       = messages_adminpriv_configmenu();

    /*
     * Populate the rest of the template
     */
    $data['action']     = xarModURL('messages','admin','config' );
    $data['authid']     = xarSecGenAuthKey();
    $data['supportshorturls']   = xarModVars::get('messages', 'SupportShortURLs');
    return $data;

}

function messages_adminpriv_configmenu()
{
    /*
     * Build the configuration submenu
     */
    $menu = array(
        array(
            'label' =>  'Config',
            'url'   =>  xarModURL('messages','admin','config' )));

    $menu[] = array(
            'label' =>  'Messages',
            'url'   =>  xarModURL('messages','admin','config',array( 'itemtype' => '1' )));


    return $menu;

}
?>
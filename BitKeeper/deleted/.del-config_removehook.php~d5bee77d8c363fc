<?php
/**
 * removeHook config for the hookbridge module.
 */
function hookbridge_adminapi_config_removehook( $args ) 
{

    if (!xarVarFetch('itemtype',   'str', $itemtype,   '',     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('authid',   'str', $authid,   '',     XARVAR_NOT_REQUIRED)) return;

    extract( $args );

    if ( isset( $authid ) && !empty($authid)  ) 
    {

        /*
         * The user confirmed the form. So save the results.
         */

        if (!xarSecConfirmAuthKey()) return;


        $data['hookenabled_remove']  = xarModGetVar('hookbridge', 'hookenabled_remove' );
        $data['hookfunctions_remove'] = unserialize(xarModGetVar('hookbridge', 'hookfunctions_remove' ));

        if (!xarVarFetch('hookenabled_remove',   'str', $hookenabled_remove,   '',     XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('hookfunctions_remove',   'array', $hookfunctions_remove,   '',     XARVAR_NOT_REQUIRED)) return;

        if ( empty( $hookenabled_remove ) or !is_numeric( $hookenabled_remove ) ) 
        {
            $hookenabled_remove = 0;
        }

        xarModSetVar('hookbridge', 'hookenabled_remove', $hookenabled_remove );
        xarModSetVar('hookbridge', 'hookfunctions_remove', serialize($hookfunctions_remove) );

        

        /*
         * Set a status message
         */
        xarSessionSetVar('hookbridge_statusmsg', xarML( 'Updated the removeHook module settings!' ) );

        /*
         * Finished. Back to the sender!
         */
        return xarResponseRedirect(
            xarModURL('hookbridge','admin','config',array(
                                                            'itemtype' => $itemtype )
                                                          ));

    } // Save the changes


}
?>
<?php
/**
 * Administration for the hookbridge module.
 */
function hookbridge_adminapi_config_main( $args ) 
{
    if (!xarVarFetch('itemtype',   'str', $itemtype,   '',     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('authid',     'str', $authid,     '',     XARVAR_NOT_REQUIRED)) return;

    extract( $args );

    if ( isset( $authid ) && !empty($authid) ) 
    {

        /*
         * The user confirmed the form. So save the results.
         */

        if (!xarSecConfirmAuthKey()) return;


        if (!xarVarFetch('supportshorturls',   'str', $supportshorturls,   '',     XARVAR_NOT_REQUIRED)) return;

        if ( empty( $supportshorturls ) or !is_numeric( $supportshorturls ) ) 
        {
            $supportshorturls = 0;
        }

        xarModSetVar('hookbridge', 'SupportShortURLs', $supportshorturls );


        if (!xarVarFetch('hookbridge_functionpath',   'str', $hookbridge_functionpath,   '',     XARVAR_NOT_REQUIRED)) return;
        xarModSetVar('hookbridge', 'HookBridge_FunctionPath', $hookbridge_functionpath );
        

        /*
         * Set a status message
         */
        xarSessionSetVar('hookbridge_statusmsg', xarML( 'Updated the global module settings!' ) );

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
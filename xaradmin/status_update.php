<?php
function twitter_admin_status_update($args)
{
    // @TODO: Add the AddTwitter mask
    // allow for whitelist of users permitted to send tweets from this account
    // if (!xarSecurityCheck('AddTwitter', 1)) { // Check whitelist (cfr. site lock)... }
    if (!xarSecurityCheck('AdminTwitter')) return;
    extract($args);

    if (!xarVarFetch('phase', 'pre:trim:lower:enum:update',
        $phase, 'form', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('status_update_text', 'pre:trim:str:1:',
        $status_update_text, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('return_url', 'pre:trim:str:1:',
        $return_url, '', XARVAR_NOT_REQUIRED)) return;

    $data = array();
    $access_token = xarSession::getVar('twitter.access_token');
    $access_token_secret = xarSession::getVar('twitter.access_token_secret');

    if (empty($access_token) || empty($access_token_secret)) {
        $data['invalid'] = xarML('You must first set the Application and Site Access Tokens');
        xarTPLSetPageTitle(xarML('Site Account Error'));
        return $data;
    }

    $data['account'] = xarMod::apiFunc('twitter', 'rest', 'account',
        array(
            'method' => 'verify_credentials',
            'access_token' => $access_token,
            'access_token_secret' => $access_token_secret,
        ));

    if (!empty($data['account']['error'])) {
        $data['invalid'] = $data['account']['error'];
        xarTPLSetPageTitle(xarML('Site Account Error'));
        return $data;
    }

    if ($phase == 'update') {
        if (!xarSecConfirmAuthKey()) return;
        if (empty($status_update_text))
            $status_update_error = 'Error: You must enter a message between 1 and 140 characters long';
        if (strlen($status_update_text) > 140) {
            // @CHECKME: run string through parser here and attempt to reduce ?
            //$status_update_text = TwitterUtil::prepstatus($status_update_text);
            //$status_update_error = xarML('Warning: Your message was concatenated, please review the result before sending');
            $status_update_error = xarML('Error: Your message cannot be longer than 140 characters');
        }
        if (empty($status_update_error)) {
            $response = xarModAPIFunc('twitter', 'rest', 'status',
                array(
                    'method' => 'update',
                    'status' => $status_update_text,
                    'access_token' => $access_token,
                    'access_token_secret' => $access_token_secret,
                    ));
            if (!is_array($response)) {
                $status_update_error = xarML('Error: Invalid response communicating with Twitter');
            } elseif (!empty($response['error'])) {
                $status_update_error = $response['error'];
            }
        }
        if (!empty($status_update_error)) {
            xarSession::setVar('twitter.status_update_text', $status_update_text);
            xarSession::setVar('twitter.status_update_error', $status_update_error);
        } else {
            $status_update_success = xarML('Success: Tweet Sent');
            xarSession::setVar('twitter.status_update_success', $status_update_success);
        }

        if (!empty($return_url))
            xarResponse::redirect($return_url);

    }

    $data['status_update_text'] = $status_update_text;
    $data['status_update_error'] = !empty($status_update_error) ? $status_update_error : '';
    $data['status_update_success'] = !empty($status_update_success) ? $status_update_success : '';
    $data['status_update_url'] = !empty($status_update_url) ? $status_update_url : xarModURL('twitter', 'admin', 'status_update');
    $data['compare'] = xarModURL('twitter', 'admin', 'status_update');
    $data['return_url'] = empty($return_url) ? xarServer::getCurrentURL() : $return_url;

    if (!xarVarFetch('tplmodule', 'pre:trim:lower:str:1:',
        $tplmodule, 'twitter', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('template', 'pre:trim:lower:str:1:',
        $template, null, XARVAR_NOT_REQUIRED)) return;

    return xarTplModule($tplmodule, 'admin', 'status_update', $data, $template);
}
?>
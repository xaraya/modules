<?php
/**
 * Twitter Module
 *
 * @package modules
 * @copyright (C) 2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Twitter Module
 * @link http://xaraya.com/index.php/release/991.html
 * @author Chris Powis (crisp@crispcreations.co.uk)
 */
function twitter_admin_account($args)
{
    if (!xarSecurityCheck('AdminTwitter')) return;

    $access_token = xarModVars::get('twitter', 'access_token');
    $access_token_secret = xarModVars::get('twitter', 'access_token_secret');

    if (empty($access_token) || empty($access_token_secret)) {
        $data['invalid'] = xarML('You must first set the Application and Site Access Tokens');
        xarTPLSetPageTitle(xarML('Site Account Error'));
        return $data;
    }

    $data = array();
    if (!xarVarFetch('tab', 'pre:trim:lower:str:1:',
        $data['tab'], null, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('method', 'pre:trim:lower:str:1:',
        $data['method'], null, XARVAR_NOT_REQUIRED)) return;

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

    $pagetitle = xarML('Site Account');
    $account_screen_name = $data['account']['screen_name'];
    $params = array();
    switch ($data['tab']) {

        case 'home':
        default:
            switch ($data['method']) {
                case 'mentions':
                    $rest_func = 'timeline';
                    $params['method'] = 'mentions';
                    $pagetitle = xarML('@#(1) mentions', $account_screen_name);
                break;

                case 'retweets':
                    if (!xarVarFetch('rt', 'pre:trim:lower:str:1:',
                        $data['rt'], null, XARVAR_NOT_REQUIRED)) return;
                    $rest_func = 'timeline';
                    switch ($data['rt']) {

                        case 'byyou':
                        default:
                            $params['method'] = 'retweeted_by_me';
                            $pagetitle = xarML('Retweeted by you');
                        break;

                        case 'byothers':
                            $params['method'] = 'retweeted_to_me';
                            $pagetitle = xarML('Retweeted by others');
                        break;

                        case 'ofyou':
                            $params['method'] = 'retweets_of_me';
                            $pagetitle = xarML('Your tweets, retweeted');
                        break;
                    }

                break;

                case 'searches':
                    if (!xarVarFetch('q', 'str:1',
                        $params['q'], null, XARVAR_NOT_REQUIRED)) return;
                    if (!empty($params['q'])) {
                        $rest_func = 'search';
                        $data['q'] = $params['q'];
                    } else {
                        $rest_func = 'saved_searches';
                    }
                    $pagetitle = xarML('Your saved searches');
                break;

                case 'lists':
                    $rest_func = 'list';
                    $params['method'] = 'index';
                    $params['user'] = $account_screen_name;
                    $pagetitle = xarML('Your lists');
                break;

                case 'timeline':
                default:
                    $rest_func = 'timeline';
                    $params['method'] = 'home_timeline';
                    $pagetitle = xarML('Home timeline');
                break;
            }
            // we show the status update form here,
            // set the token/secret for use by the status_update function
            // (Do NOT set these hidden in the form input)
            xarSession::setVar('twitter.access_token', $access_token);
            xarSession::setVar('twitter.access_token_secret', $access_token_secret);
            // set the status_update url for the form action
            $data['status_update_url'] = xarModURL('twitter', 'admin', 'status_update');
            $data['return_url'] = xarServer::getCurrentURL();
        break;

        case 'profile':
            switch ($data['method']) {
                case 'favorites':
                    $rest_func = 'favorite';
                    $pagetitle = xarML('Favorites');
                break;

                case 'following':
                    $rest_func = 'user';
                    $params['method'] = 'friends';
                    $pagetitle = xarML('Following');
                break;

                case 'followers':
                    $rest_func = 'user';
                    $params['method'] = 'followers';
                    $pagetitle = xarML('Followers');
                break;

                case 'lists':
                    $rest_func = 'list';
                    $params['method'] = 'index';
                    $params['user'] = $account_screen_name;
                    $pagetitle = xarML('Lists');
                break;

                case 'settings':
                    if (!xarVarFetch('phase', 'pre:trim:lower:enum:update',
                        $phase, 'modify', XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('rt', 'pre:trim:lower:str:1:',
                        $data['rt'], null, XARVAR_NOT_REQUIRED)) return;
                    if ($phase == 'update') {
                        if (!xarSecConfirmAuthKey()) return;
                        if (!xarVarFetch('return_url', 'pre:trim:str:1:',
                            $return_url, '', XARVAR_NOT_REQUIRED)) return;

                        switch ($data['rt']) {
                            default:
                                if (!xarVarFetch('twitter_name', 'pre:trim:str:1:',
                                    $twitter_name, '', XARVAR_NOT_REQUIRED)) return;
                                if (!xarVarFetch('twitter_location', 'pre:trim:str:1:',
                                    $twitter_location, '', XARVAR_NOT_REQUIRED)) return;
                                if (!xarVarFetch('twitter_url', 'pre:trim:str:0:',
                                    $twitter_url, '', XARVAR_NOT_REQUIRED)) return;
                                if (!xarVarFetch('twitter_description', 'pre:trim:str:0:',
                                    $twitter_description, '', XARVAR_NOT_REQUIRED)) return;
                                $changed = false;
                                if ($account['name'] != $twitter_name ||
                                    $account['location'] != $twitter_location ||
                                    $account['url'] != $twitter_url ||
                                    $account['description'] != $twitter_description) {
                                    $changed = true;
                                }
                                if ($changed) {
                                    if (strlen($twitter_name) > 20)
                                        $invalid['twitter_name'] = xarML('Name must be between 1 and 20 characters');
                                    if (strlen($twitter_description) > 160)
                                        $invalid['twitter_description'] = xarML('Description must be 160 characters or less');
                                    if (empty($invalid)) {
                                        $response = xarMod::apiFunc('twitter', 'rest', 'account',
                                            array(
                                                'method' => 'update_profile',
                                                'name' => $twitter_name,
                                                'location' => $twitter_location,
                                                'url' => $twitter_url,
                                                'description' => $twitter_description,
                                                'access_token' => $access_token,
                                                'access_token_secret' => $access_token_secret,
                                            ));
                                    }
                                }
                                if (empty($invalid))
                                    xarSession::setVar('twitter.update_profile_success',
                                        xarML('Success: Profile Updated'));
                            break;
                        }
                        if (empty($return_url))
                            $return_url = xarModURL('twitter', 'admin', 'account');

                        xarResponse::redirect($return_url);
                    }

                break;

                case 'timeline':
                default:
                    $rest_func = 'timeline';
                    $params['method'] = 'user_timeline';
                    $pagetitle = xarML('Home');
                break;
            }
        break;

        case 'messages':
            switch ($data['method']) {
                default:
                    $rest_func = 'direct_messages';
                    $pagetitle = xarML('Messages');
                break;
            }
        break;

    }

    if (!empty($rest_func)) {
        $params['access_token'] = $access_token;
        $params['access_token_secret'] = $access_token_secret;
        //$params['expires'] = 1;
        $data['content'] = xarMod::apiFunc('twitter', 'rest', $rest_func, $params);
        //print_r($data['content']);
    }

    xarTplSetPageTitle(xarVarPrepForDisplay($pagetitle));

    return $data;
}
?>
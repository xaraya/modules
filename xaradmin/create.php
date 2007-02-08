<?php
/**
 * Articles module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 */
/**
 * create item from xarModFunc('articles','admin','new')
 *
 * @param id     ptid       The publication Type ID for this new article
 * @param array  new_cids   An array with the category ids for this new article (OPTIONAL)
 * @param string preview    Are we gonna see a preview?
 * @param string save       Call the save action
 * @param string return_url The URL to return to
 * @throws BAD_PARAM
 * @return  bool true on success, or mixed on failure
 */
function articles_admin_create()
{
    // Get parameters
    if (!xarVarFetch('ptid',     'id',    $ptid)) {return;}
    if (!xarVarFetch('new_cids', 'array', $cids,    NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('preview',  'str',   $preview, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('save',     'str',   $save, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('return_url', 'str:1', $return_url, NULL, XARVAR_NOT_REQUIRED)) {return;}
    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    $pubtypes = xarModAPIFunc('articles','user','getpubtypes');
    if (!isset($pubtypes[$ptid])) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'publication type', 'admin', 'create',
                    'Articles');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

// TODO: switch to DD object style
    $article = array();
    $invalid = array();
    if (xarModIsHooked('uploads', 'articles', $ptid)) {
        xarVarSetCached('Hooks.uploads','ishooked',1);
    }
    $properties = array();
    foreach ($pubtypes[$ptid]['config'] as $field => $value) {
        if (!empty($value['label']) && !empty($value['input'])) {
            if (!isset($value['validation'])) {
                $value['validation'] = '';
            }
            $properties[$field] = xarModAPIFunc('dynamicdata','user','getproperty',
                                                 array('name' => $field,
                                                       'type' => $value['format'],
                                                       'validation' => $value['validation']));
            $check = $properties[$field]->checkInput($field);
            if (!$check) {
                $article[$field] = '';
                $invalid[$field] = $properties[$field]->invalid;
                $preview = 1;
            } else {
                $article[$field] = $properties[$field]->value;
            }
        }
        if (!isset($article[$field])) {
            $article[$field] = '';
        }
    }

    $article['ptid'] = $ptid;

    // check that we have a title when we need one, or fill in a dummy one
    if (empty($article['title'])) {
        if (empty($pubtypes[$ptid]['config']['title']['label'])) {
            $article['title'] = ' ';
        } elseif (empty($invalid['title'])) {
            // show this to the user
            $invalid['title'] = xarML('This field is required');
        }
    }
    if (empty($article['pubdate'])) {
        $article['pubdate'] = time();
    }

// TODO: make $status dependent on permissions ?
    if (empty($article['status'])) {
        $settings = unserialize(xarModGetVar('articles', 'settings.'.$ptid));
        if (isset($settings['defaultstatus'])) {
            $article['status'] = $settings['defaultstatus'];
        } elseif (empty($pubtypes[$ptid]['config']['status']['label'])) {
            $article['status'] = 2;
        } else {
            $article['status'] = 0;
        }
    }

    $article['authorid'] = xarUserGetVar('uid');
    if (empty($article['authorid'])) {
        $article['authorid'] = _XAR_ID_UNREGISTERED;
    }

    if (empty($article['language'])) {
        $article['language'] = xarMLSGetCurrentLocale();
    }

    if (!empty($cids) && count($cids) > 0) {
        $article['cids'] = array_values(preg_grep('/\d+/',$cids));
    } else {
        $article['cids'] = array();
    }

    // for preview
    $article['pubtypeid'] = $ptid;
    $article['aid'] = 0;

    if ($preview || count($invalid) > 0) {
        $data = xarModFunc('articles','admin','new',
                             array('preview' => true,
                                   'article' => $article,
                                   'return_url' => $return_url,
                                   'invalid' => $invalid));
        unset($article);
        if (is_array($data)) {
            return xarTplModule('articles','admin','new',$data);
        } else {
            return $data;
        }
    }

    // call transform input hooks
    $article['transform'] = array('summary','body','notes');
    $article = xarModCallHooks('item', 'transform-input', 0, $article,
                               'articles', $ptid);

    // Pass to API
    $aid = xarModAPIFunc('articles', 'admin', 'create', $article);

    if ($aid == false) {
        // Throw back any system exceptions (e.g. database failure)
        if (xarCurrentErrorType() == XAR_SYSTEM_EXCEPTION) {
            return; // throw back
        }
        // Handle the user exceptions yourself
        $status = xarML('Creating article failed');
        // Get the information about the exception (in HTML or string format)
        // $reason = xarCurrentErrorHTML();
        $reason = xarCurrentError();
        if (!empty($reason)) {
            $status .= '<br /><br />'. xarML('Reason') .' : '. $reason->toString();
        }
        // Free the exception to tell Xaraya that you handled it
        xarErrorFree();
        return $status;
    }

    // Success
    xarSessionSetVar('statusmsg', xarML('Article Created'));

    // Save and continue editing via feature request.
    if (isset($save)){
        if (xarSecurityCheck('EditArticles',0,'Article',$ptid.':All:All:All')) {
            xarResponseRedirect(xarModURL('articles', 'admin', 'modify',
                                          array('aid' => $aid)));
        } else {
            xarResponseRedirect(xarModURL('articles', 'user', 'view',
                                          array('ptid' => $ptid)));
        }
    }

    if (!empty($return_url)) {
        xarResponseRedirect($return_url);
        return true;
    }

    // if we can edit articles, go to admin view, otherwise go to user view
    if (xarSecurityCheck('EditArticles',0,'Article',$ptid.':All:All:All')) {
        xarResponseRedirect(xarModURL('articles', 'admin', 'view',
                                      array('ptid' => $ptid)));
    } else {
        xarResponseRedirect(xarModURL('articles', 'user', 'view',
                                      array('ptid' => $ptid)));
    }

    return true;
}

?>

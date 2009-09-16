<?php
/**
 * Articles module
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
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
 * @param string preview    Are we gonna see a preview? (OPTIONAL) 
 * @param string save       Call the save action, form stays open (OPTIONAL)
 * @param string view       Call the view action, show newly created article (OPTIONAL)
 * @param string return_url The URL to return to (OPTIONAL)
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
    if (!xarVarFetch('view',     'str',   $view,    NULL, XARVAR_NOT_REQUIRED)) {return;}    
    if (!xarVarFetch('return_url', 'str:1', $return_url, NULL, XARVAR_NOT_REQUIRED)) {return;}
    // Confirm authorisation code
    try {
        $confirm = xarSecConfirmAuthKey();
        if (!$confirm) return;
    } catch (ForbiddenOperationException $e) {
        // Catch exception and fall back to preview
        $msg = $e->getMessage() . "<br />";
        $msg .= xarML('Article was <strong>NOT</strong> saved, please retry.');
        // Save the error message if we are not in preview
        if (!isset($preview)) {
            xarSession::setVar('statusmsg', $msg);
        }
        $preview = 1;
    }

    $pubtypes = xarModAPIFunc('articles','user','getpubtypes');
    if (!isset($pubtypes[$ptid])) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'publication type', 'admin', 'create',
                    'Articles');
        throw new BadParameterException(null,$msg);
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
                                                       'configuration' => $value['validation']));
            $check = $properties[$field]->checkInput($field);
            if (!$check) {
echo var_dump($properties[$field]);
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
        $settings = unserialize(xarModVars::get('articles', 'settings.'.$ptid));
        if (isset($settings['defaultstatus'])) {
            $article['status'] = $settings['defaultstatus'];
        } elseif (empty($pubtypes[$ptid]['config']['status']['label'])) {
            $article['status'] = 2;
        } else {
            $article['status'] = 0;
        }
    }

    // Default the authorid to the current author if either not already set, or the authorid
    // field does not allow input when creating a new article.
    if (empty($article['authorid']) || empty($pubtypes[$ptid]['config']['authorid']['input'])) {
        $article['authorid'] = xarUserGetVar('id');
    }
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
    try {
        $aid = xarModAPIFunc('articles', 'admin', 'create', $article);
        if (empty($aid)) return;
    } catch (Exception $e) {
        // TODO: Avoid dataloss with falling back to preview
        // Catch exception and fall back to preview
        $status = xarML('Creating article failed');
        $status .= '<br /><br />'. xarML('Reason') .' : '. $e->getMessage();
        // Save the error message if we are not in preview
        if (!isset($preview)) {
            xarSession::setVar('statusmsg', $status);
        }
        return $status;
    }

    // Success
    xarSession::setVar('statusmsg', xarML('Article Created'));

    // Save and continue editing via feature request.
    if (isset($save)){
        if (xarSecurityCheck('EditArticles',0,'Article',$ptid.':All:All:All')) {
            xarResponse::Redirect(xarModURL('articles', 'admin', 'modify',
                                          array('aid' => $aid)));
        } else {
            xarResponse::Redirect(xarModURL('articles', 'user', 'view',
                                          array('ptid' => $ptid)));
        }
    }
    // Save and view the new article
    if (isset($view)){
        xarResponse::Redirect(xarModURL('articles', 'user', 'display',
                                      array('ptid' => $ptid,
                                            'aid' => $aid)));   
    }

    if (!empty($return_url)) {
        xarResponse::Redirect($return_url);
        return true;
    }

    // if we can edit articles, go to admin view, otherwise go to user view
    if (xarSecurityCheck('EditArticles',0,'Article',$ptid.':All:All:All')) {
        xarResponse::Redirect(xarModURL('articles', 'admin', 'view',
                                      array('ptid' => $ptid)));
    } else {
        xarResponse::Redirect(xarModURL('articles', 'user', 'view',
                                      array('ptid' => $ptid)));
    }

    return true;
}

?>

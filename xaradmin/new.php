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
 * Prepare form for new article
 *
 * This function presents the template from which the article is created
 * @param int    ptid       The publication type id, overrides an itemtype value
 * @param string catid      The category id this article will belong to
 * @param int    itemtype   The itemtype (optional)
 * @param string return_url The url to return to
 * @return mixed call to template with data array and name of template to use
 */
function articles_admin_new($args)
{
    extract($args);

    if (!xarVarFetch('ptid',        'id',    $ptid,       NULL,  XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('catid',       'str',   $catid,      NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemtype',    'id',    $itemtype,   NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('return_url',  'str:1', $return_url, NULL, XARVAR_NOT_REQUIRED)) {return;}

    if (!empty($preview) && isset($article)) {
        // Use given pubtype from article
        $ptid = $article['ptid'];
    } elseif (!isset($ptid) && !empty($itemtype) && is_numeric($itemtype)) {
        // Use itemtype parameter if given
        $ptid = $itemtype;
    } elseif (!isset($ptid)) {
        // Use defaultpubtype now. This var may even be NULL
        $ptid = xarModGetVar('articles', 'defaultpubtype');
    }

    $data = array();
    $data['ptid'] = $ptid;
    $data['catid'] = $catid;

    if (!isset($article)) {
        $article = array();
    }
    if (!isset($articles['cids']) && !empty($catid)) {
        $article['cids'] = preg_split('/[ +-]/',$catid);
    }

    $pubtypes = xarModAPIFunc('articles','user','getpubtypes');

    // Security check
    if (empty($ptid)) {
        $ptid = '';
        if (!xarSecurityCheck('SubmitArticles')) {
               $msg = xarML('You have no permission to submit Articles');
                throw new ForbiddenOperationException(null, $msg);
        }
    } else {
        if (isset($article['cids']) && count($article['cids']) > 0) {
            foreach ($article['cids'] as $cid) {
                if (!xarSecurityCheck('SubmitArticles',1,'Article',"$ptid:$cid:All:All")) {
                    $catinfo = xarModAPIFunc('categories', 'user', 'getcatinfo',
                                             array('cid' => $cid));
                    if (empty($catinfo['name'])) {
                        $catinfo['name'] = $cid;
                    }
                    $msg = xarML('You have no permission to submit #(1) in category #(2)',
                                 $pubtypes[$ptid]['descr'],$catinfo['name']);
                    throw new ForbiddenOperationException(null, $msg);
                }
            }
        } else {
            if (!xarSecurityCheck('SubmitArticles',1,'Article',"$ptid:All:All:All")) {
                $msg = xarML('You have no permission to submit #(1)',
                             $pubtypes[$ptid]['descr']);
                throw new ForbiddenOperationException(null, $msg);
            }
        }
    }
    // Prepare preview
    if (!empty($preview)) {
        // Use articles user GUI function (not API) for preview
        if (!xarModLoad('articles','user')) return;
        $preview = xarModFunc('articles', 'user', 'display',
                             array('preview' => true, 'article' => $article));
    } else {
        $preview = '';
    }
    $data['preview'] = $preview;

    $hooks = array();
    if (!empty($ptid)) {
        // Uploads hasn't an itemnew hook so it will not appear in $hooks
        if (xarModIsHooked('uploads', 'articles', $ptid)) {
            xarVarSetCached('Hooks.uploads', 'ishooked', 1);
        }
        // preset some variables for hook modules
        $article['module'] = 'articles';
        $article['itemid'] = 0;
        $article['itemtype'] = $ptid;
        $hooks = xarModCallHooks('item','new','',$article);
    }
    $data['hooks'] = $hooks;

    // Array containing the different labels
    $labels = array();

    // Show publication type
    $pubfilters = array();
    foreach ($pubtypes as $id => $pubtype) {
        $pubitem = array();
        if ($id == $ptid) {
            $pubitem['plink'] = '';
        } else {
            if (!xarSecurityCheck('SubmitArticles',0,'Article',$id.':All:All:All')) {
                continue;
            }
            $pubitem['plink'] = xarModURL('articles','admin','new',
                                          array('ptid' => $id,
                                                'catid' => $catid));
        }
        $pubitem['ptitle'] = $pubtype['descr'];
        $pubfilters[] = $pubitem;
    }
    $data['pubfilters'] = $pubfilters;

    // Array containing the different values (except the article fields)
    // Hb: See comment in template on this var
    $values = array();

    // TODO - language

// Note : this determines which fields are really shown in the template !!!
    // Show actual data fields
    $fields = array();
    $data['withupload'] = 0;
    if (!empty($ptid)) {
        $settings = unserialize(xarModGetVar('articles', 'settings.'.$ptid));
        $data['defaultstatus'] = $settings['defaultstatus'];
    // TODO: make order dependent on pubtype or not ?
    //    foreach ($pubtypes[$ptid]['config'] as $field => $value) {}
        $pubfields = xarModAPIFunc('articles','user','getpubfields');
        foreach ($pubfields as $field => $dummy) {
            $value = $pubtypes[$ptid]['config'][$field];
            if (empty($value['label']) || empty($value['input'])) {
                continue;
            }
            $input = array();
            $input['name'] = $field;
            $input['type'] = $value['format'];
            $input['id'] = $field;
            if (!empty($preview) && isset($article[$field])) {
                $input['value'] = $article[$field];
            } elseif ($field == 'pubdate') {
                // default publication time is now
                $input['value'] = time();
            } elseif ($field == 'status' && isset($settings['defaultstatus'])) {
                // default status (only if allowed on input)
                $input['value'] = $settings['defaultstatus'];
            } else {
                $input['value'] = '';
            }
            if (isset($value['validation'])) {
                $input['validation'] = $value['validation'];
            }

            if ($input['type'] == 'fileupload' || $input['type'] == 'textupload' ) {
                $data['withupload'] = 1;
            }
            if (!empty($preview) && isset($invalid) && !empty($invalid[$field])) {
                $input['invalid'] = $invalid[$field];
            }
            $fields[$field] = array('label' => $value['label'], 'id' => $field,
                                    'definition' => $input);
        }
    }
    $data['fields'] = $fields;

    if (!empty($ptid) && empty($data['withupload']) &&
        (xarVarIsCached('Hooks.dynamicdata','withupload') || xarModIsHooked('uploads', 'articles', $ptid)) ) {
        $data['withupload'] = 1;
    }

    // Show allowable HTML
    $data['allowedhtml'] = '';
    foreach (xarConfigGetVar('Site.Core.AllowableHTML') as $k=>$v) {
        if ($v) {
            $data['allowedhtml'] .= '&lt;' . $k . '&gt; ';
        }
    }

    if (!empty($ptid)) {
        $formhooks = articles_user_formhooks($ptid);
        $data['formhooks'] = $formhooks;
    }

    $data['previewlabel'] = xarVarPrepForDisplay(xarML('Preview'));
    $data['addlabel'] = xarVarPrepForDisplay(xarML('Add Article'));
    $data['authid'] = xarSecGenAuthKey('articles');
    $data['return_url'] = $return_url;
    $data['values'] = $values;

    if (!empty($ptid)) {
        $template = $pubtypes[$ptid]['name'];
        xarTplSetPageTitle(xarML('New #(1)', $pubtypes[$ptid]['descr']));
    } else {
// TODO: allow templates per category ?
       $template = null;
       xarTplSetPageTitle(xarML('New'));
    }

    return xarTplModule('articles', 'admin', 'new', $data, $template);
}

?>

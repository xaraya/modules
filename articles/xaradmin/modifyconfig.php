<?php

/**
 * Modify configuration
 */
function articles_admin_modifyconfig()
{
    // Get parameters
    if(!xarVarFetch('ptid', 'isset', $ptid, NULL, XARVAR_DONT_SET)) {return;}

    // Security check
    if (empty($ptid)) {
        $ptid = '';
        if (!xarSecurityCheck('AdminArticles')) return;
    } else {
        if (!xarSecurityCheck('AdminArticles',1,'Article',"$ptid:All:All:All")) return;
    }

    // Get the article settings for this publication type
    if (!empty($ptid)) {
        $settings = unserialize(xarModGetVar('articles', 'settings.'.$ptid));
    } else {
        $string = xarModGetVar('articles', 'settings');
        if (!empty($string)) {
            $settings = unserialize($string);
        }
    }
    $data = array();
    $data['ptid'] = $ptid;

    $pubtypes = xarModAPIFunc('articles','user','getpubtypes');

    if (isset($settings) && is_array($settings)) {
        $data['itemsperpage']           = empty($settings['itemsperpage']) ? 20 : $settings['itemsperpage'];
        $data['adminitemsperpage']      = empty($settings['adminitemsperpage']) ? 20 : $settings['adminitemsperpage'];
        $data['numcols']                = $settings['number_of_columns'];
        $data['defaultview']            = $settings['defaultview'];
        $data['showcategories']         = !empty($settings['showcategories']) ? 'checked' : '';
        $data['showprevnext']           = !empty($settings['showprevnext']) ? 'checked' : '';
        $data['showcomments']           = !empty($settings['showcomments']) ? 'checked' : '';
        $data['showhitcounts']          = !empty($settings['showhitcounts']) ? 'checked' : '';
        $data['showratings']            = !empty($settings['showratings']) ? 'checked' : '';
        $data['showarchives']           = !empty($settings['showarchives']) ? 'checked' : '';
        $data['showmap']                = !empty($settings['showmap']) ? 'checked' : '';
        $data['showpublinks']           = !empty($settings['showpublinks']) ? 'checked' : '';
        $data['dotransform']            = !empty($settings['dotransform']) ? 'checked' : '';
        $data['titletransform']         = !empty($settings['titletransform']) ? 'checked' : '';
        $data['prevnextart']            = !empty($settings['prevnextart']) ? 'checked' : '';
        $data['page_template']          = isset($settings['page_template']) ? $settings['page_template'] : '';
        $data['defaultstatus']          = isset($settings['defaultstatus']) ? $settings['defaultstatus'] : null;
    }
    if (!isset($data['itemsperpage'])) {
        $data['itemsperpage'] = 20;
    }
    if (!isset($data['numcols'])) {
        $data['numcols'] = 0;
    }
    if (!isset($data['defaultview'])) {
        $data['defaultview'] = 1;
    }
    if (!isset($data['showcategories'])) {
        $data['showcategories'] = '';
    }
    if (!isset($data['showprevnext'])) {
        $data['showprevnext'] = '';
    }
    if (!isset($data['showcomments'])) {
        $data['showcomments'] = 'checked';
    }
    if (!isset($data['showhitcounts'])) {
        $data['showhitcounts'] = 'checked';
    }
    if (!isset($data['showratings'])) {
        $data['showratings'] = '';
    }
    if (!isset($data['showarchives'])) {
        $data['showarchives'] = 'checked';
    }
    if (!isset($data['showmap'])) {
        $data['showmap'] = 'checked';
    }
    if (!isset($data['showpublinks'])) {
        $data['showpublinks'] = '';
    }
    if (!isset($data['dotransform'])) {
        $data['dotransform'] = '';
    }
    if (!isset($data['titletransform'])) {
        $data['titletransform'] = '';
    }
    if (!isset($data['prevnextart'])) {
        $data['prevnextart'] = '';
    }
    if (!isset($data['page_template'])) {
        $data['page_template'] = '';
    }
    if (!isset($data['defaultstatus'])) {
        if (empty($ptid)) {
            $data['defaultstatus'] = 2;
        } elseif (!isset($pubtypes[$ptid])) {
            $data['defaultstatus'] = 2;
        } else {
            if (empty($pubtypes[$ptid]['config']['status']['label'])) {
                $data['defaultstatus'] = 2;
            } else {
                $data['defaultstatus'] = 0;
            }
        }
    }
    if (empty($ptid) || empty($pubtypes[$ptid]['config']['status']['label'])) {
        $data['withstatus'] = 0;
    } else {
        $data['withstatus'] = 1;
    }

    // call modifyconfig hooks with module + itemtype
    $hooks = xarModCallHooks('module', 'modifyconfig', 'articles',
                             array('module'   => 'articles',
                                   'itemtype' => $ptid));

    if (empty($hooks)) {
        $data['hooks'] = array('categories' => xarML('You can assign base categories by enabling the categories hooks for articles...'));
    } else {
        $data['hooks'] = $hooks;
    }

    $data['updatelabel'] = xarML('Update Configuration');

    // Get the list of current hooks for item displays
    $hooklist = xarModGetHookList('articles','item','display',$ptid);
    $seenhook = array();
    foreach ($hooklist as $hook) {
        $seenhook[$hook['module']] = 1;
    }

    if (!empty($seenhook['comments'])) {
        $data['showcommentsoptions'] = true;
    }
    else {
        $data['showcommentsoptions'] = false;
    }

    if (!empty($seenhook['hitcount'])) {
        $data['showhitcountsoptions'] = true;
    }
    else {
        $data['showhitcountsoptions'] = false;
    }
    if (!empty($seenhook['ratings'])) {
        $data['showratingsoptions'] = true;
    }
    else {
        $data['showratingsoptions'] = false;
    }

    $viewoptions = array();
    $viewoptions[] = array('value' => 1, 'label' => xarML('Latest Items'));

    // get root categories for this publication type
    if (!empty($ptid)) {
        $catlinks = xarModAPIFunc('articles',
                                 'user',
                                 'getrootcats',
                                 array('ptid' => $ptid));
    // Note: if you want to use a *combination* of categories here, you'll
    //       need to use something like 'c15+32'
        foreach ($catlinks as $catlink) {
            $viewoptions[] = array('value' => 'c' . $catlink['catid'],
                                   'label' => xarML('Browse in') . ' ' .
                                              $catlink['cattitle']);
        }
    }
    $data['viewoptions'] = $viewoptions;

    // Create a link for each publication type
    $pubfilters = array();

    // Link to default settings
    $pubitem = array();
    $pubitem['ptitle'] = xarML('Defaults');
    if (empty($ptid)) {
        $pubitem['plink'] = '';
    } else {
        $pubitem['plink'] = xarModURL('articles','admin','modifyconfig');
    }
    $pubitem['pid'] = '';
    $pubfilters[] = $pubitem;

    // Links to settings per publication type
    foreach ($pubtypes as $id => $pubtype) {
        if (!xarSecurityCheck('AdminArticles',0,'Article',"$id:All:All:All")) {
            continue;
        }
        $pubitem = array();
        if ($id == $ptid) {
            $pubitem['plink'] = '';
        } else {
            $pubitem['plink'] = xarModURL('articles','admin','modifyconfig',
                                         array('ptid' => $id));
        }
        $pubitem['ptitle'] = $pubtype['descr'];
        $pubitem['pid'] = $id;
        $pubfilters[] = $pubitem;
    }
    $data['pubfilters'] = $pubfilters;

    if (empty($ptid)) {
        $data['shorturls'] = xarModGetVar('articles','SupportShortURLs') ? 'checked' : '';
        $data['defaultpubtype'] = xarModGetVar('articles', 'defaultpubtype');
        if (empty($data['defaultpubtype'])) {
            $data['defaultpubtype'] = '';
        }
        $data['sortpubtypes'] = xarModGetVar('articles', 'sortpubtypes');
        if (empty($data['sortpubtypes'])) {
            $data['sortpubtypes'] = 'id';
            xarModSetVar('articles','sortpubtypes','id');
        }
    }

    $data['statusoptions'] = array(
                                   array('value' => 0, 'label' => xarML('Submitted')),
                                   array('value' => 1, 'label' => xarML('Rejected')),
                                   array('value' => 2, 'label' => xarML('Approved')),
                                   array('value' => 3, 'label' => xarML('Front Page')),
                                  );

    // Module alias for short URLs
    if (!empty($ptid)) {
        $data['alias'] = $pubtypes[$ptid]['name'];
    } else {
        $data['alias'] = 'frontpage';
    }
    $modname = xarModGetAlias($data['alias']);
    if ($modname == 'articles') {
        $data['usealias'] = true;
    } else {
        $data['usealias'] = false;
    }
    $data['authid'] = xarSecGenAuthKey();

    // Return the template variables defined in this function
    return $data;
}

?>

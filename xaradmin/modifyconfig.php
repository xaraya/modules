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
    //Sometimes $settings can be set but $string can return empty eg importing a pubtype
    //Let's make provision for this
    $string=''; //initialize
    if (!empty($ptid)) {
        $string = xarModVars::get('articles', 'settings.'.$ptid);
    } else {
        $string = xarModVars::get('articles', 'settings');

    }
    if (!empty($string)) {
        $settings = unserialize($string);
    }
    $data = array();
    $data['ptid'] = $ptid;

    $pubtypes = xarModAPIFunc('articles','user','getpubtypes');

    if (isset($settings) && is_array($settings)) {
        $data['itemsperpage']           = empty($settings['itemsperpage']) ? 20 : $settings['itemsperpage'];
        $data['adminitemsperpage']      = empty($settings['adminitemsperpage']) ? 20 : $settings['adminitemsperpage'];
        $data['numcols']                = empty($settings['number_of_columns']) ? 1 : $settings['number_of_columns'];
        $data['defaultview']            = $settings['defaultview'];
        // Note: the current template uses the variables both for testing and the value attribute for the tag, dont use true/false to be sure
        $data['showcategories']         = !empty($settings['showcategories']) ? 1 : 0;
        $data['showkeywords']           = !empty($settings['showkeywords']) ? 1 : 0;
        $data['showcatcount']           = !empty($settings['showcatcount']) ? 1 : 0;
        $data['showprevnext']           = !empty($settings['showprevnext']) ? 1 : 0;
        $data['showcomments']           = !empty($settings['showcomments']) ? 1 : 0;
        $data['showhitcounts']          = !empty($settings['showhitcounts']) ? 1 : 0;
        $data['showratings']            = !empty($settings['showratings']) ? 1 : 0;
        $data['showarchives']           = !empty($settings['showarchives']) ? 1 : 0;
        $data['showmap']                = !empty($settings['showmap']) ? 1 : 0;
        $data['showpublinks']           = !empty($settings['showpublinks']) ? 1 : 0;
        $data['showpubcount']           = (isset($settings['showpubcount']) && empty($settings['showpubcount'])) ? 0 : 1;
        $data['dotransform']            = !empty($settings['dotransform']) ? 1 : 0;
        $data['titletransform']         = !empty($settings['titletransform']) ? 1 : 0;
        $data['prevnextart']            = !empty($settings['prevnextart']) ? 1 : 0;
        $data['page_template']          = isset($settings['page_template']) ? $settings['page_template'] : '';
        $data['defaultstatus']          = isset($settings['defaultstatus']) ? $settings['defaultstatus'] : null;
        $data['defaultsort']            = !empty($settings['defaultsort']) ? $settings['defaultsort'] : 'date';
        $data['usetitleforurl']         = !empty($settings['usetitleforurl']) ? $settings['usetitleforurl'] : 0;
         $data['checkpubdate']           = !empty($settings['checkpubdate']) ? 1 : 0;
    }
    if (!isset($data['usecheckoutin'])) {
        $data['usecheckoutin'] = 0;
    }
    if (!isset($data['itemsperpage'])) {
        $data['itemsperpage'] = 20;
    }
    if (!isset($data['adminitemsperpage'])) {
        $data['adminitemsperpage'] = 20;
    }
    if (!isset($data['numcols'])) {
        $data['numcols'] = 0;
    }
    if (!isset($data['defaultview'])) {
        $data['defaultview'] = 1;
    }
    if (!isset($data['showcategories'])) {
        $data['showcategories'] = 0;
    }
    if (!isset($data['showkeywords'])) {
        $data['showkeywords'] = 0;
    }
    if (!isset($data['showcatcount'])) {
        $data['showcatcount'] = 0;
    }
    if (!isset($data['showprevnext'])) {
        $data['showprevnext'] = 0;
    }
    if (!isset($data['showcomments'])) {
        $data['showcomments'] = 1;
    }
    if (!isset($data['showhitcounts'])) {
        $data['showhitcounts'] = 1;
    }
    if (!isset($data['showratings'])) {
        $data['showratings'] = 0;
    }
    if (!isset($data['showarchives'])) {
        $data['showarchives'] = 1;
    }
    if (!isset($data['showmap'])) {
        $data['showmap'] = 1;
    }
    if (!isset($data['showpublinks'])) {
        $data['showpublinks'] = 0;
    }
    if (!isset($data['showpubcount'])) {
        $data['showpubcount'] = 1;
    }
    if (!isset($data['dotransform'])) {
        $data['dotransform'] = 0;
    }
    if (!isset($data['titletransform'])) {
        $data['titletransform'] = 0;
    }
    if (!isset($data['prevnextart'])) {
        $data['prevnextart'] = 0;
    }
    if (!isset($data['page_template'])) {
        $data['page_template'] = '';
    }
    if (!isset($data['checkpubdate'])) {
        $data['checkpubdate'] = 0;
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
    if (!isset($data['usetitleforurl'])) {
        $data['usetitleforurl'] = 0;
    }
    if (!isset($data['defaultsort'])) {
        $data['defaultsort'] = 'date';
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
    if (!empty($seenhook['keywords'])) {
        $data['showkeywordsoptions'] = true;
    }
    else {
        $data['showkeywordsoptions'] = false;
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
        $data['shorturls'] = xarModVars::get('articles','SupportShortURLs') ? true : false;
        $data['ptypenamechange'] = xarModVars::get('articles','ptypenamechange') ? true : false;

        $data['defaultpubtype'] = xarModVars::get('articles', 'defaultpubtype');
        if (empty($data['defaultpubtype'])) {
            $data['defaultpubtype'] = '';
        }
        $data['sortpubtypes'] = xarModVars::get('articles', 'sortpubtypes');
        if (empty($data['sortpubtypes'])) {
            $data['sortpubtypes'] = 'id';
            xarModVars::set('articles','sortpubtypes','id');
        }
    }

    $data['statusoptions'] = array();
    $states = xarModAPIFunc('articles','user','getstates');
    foreach ($states as $id => $name) {
        $data['statusoptions'][] = array('value' => $id, 'label' => $name);
    }

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

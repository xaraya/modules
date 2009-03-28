<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * add new article
 *
 * This function presents the template from which the article is created
 * @param int ptid The publication type id
 * @param string catid The category id this article will belong to
 * @param id itemtype the itemtype, if forced
 * @param string return_url The url to return to
 * @return mixed call to template with data array and name of template to use
 */

sys::import('modules.dynamicdata.class.objects.master');

function publications_user_new($args)
{
    extract($args);

    // Get parameters
    if (!xarVarFetch('ptid',        'id',    $data['ptid'],       xarModVars::get('publications', 'defaultpubtype'),  XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('catid',       'str',   $catid,      NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemtype',    'id',    $itemtype,   NULL, XARVAR_NOT_REQUIRED)) {return;}

/*    $data['catid'] = $catid;

    if (!isset($article)) {
        $article = array();
    }
    if (!isset($publications['cids']) && !empty($catid)) {
        $article['cids'] = preg_split('/[ +-]/',$catid);
    }

    $pubtypes = xarModAPIFunc('publications','user','get_pubtypes');

    if (isset($article['cids']) && count($article['cids']) > 0) {
        foreach ($article['cids'] as $cid) {
            if (!xarSecurityCheck('SubmitPublications',1,'Publication',$data['ptid'] . ":$cid:All:All")) {
                $catinfo = xarModAPIFunc('categories', 'user', 'getcatinfo',
                                         array('cid' => $cid));
                if (empty($catinfo['name'])) {
                    $catinfo['name'] = $cid;
                }
                $msg = xarML('You have no permission to submit #(1) in category #(2)',
                             $pubtypes[$data['ptid']]['description'],$catinfo['name']);
                throw new ForbiddenOperationException(null, $msg);
            }
        }
    } else {
        if (!xarSecurityCheck('SubmitPublications',1,'Publication',$data['ptid'] . ":All:All:All")) {
            $msg = xarML('You have no permission to submit #(1)',
                         $pubtypes[$data['ptid']]['description']);
            throw new ForbiddenOperationException(null, $msg);
        }
    }

    if (!empty($preview)) {
        // Use publications user GUI function (not API) for preview
        if (!xarModLoad('publications','user')) return;
        $preview = xarModFunc('publications', 'user', 'display',
                             array('preview' => true, 'article' => $article));
    } else {
        $preview = '';
    }
    $data['preview'] = $preview;


    // Array containing the different labels
    $labels = array();

    // Show publication type
    $pubfilters = array();
    foreach ($pubtypes as $id => $pubtype) {
        $pubitem = array();
        if ($id == $data['ptid']) {
            $pubitem['plink'] = '';
        } else {
            if (!xarSecurityCheck('SubmitPublications',0,'Publication',$id.':All:All:All')) {
                continue;
            }
            $pubitem['plink'] = xarModURL('publications','admin','new',
                                          array('ptid' => $id,
                                                'catid' => $catid));
        }
        $pubitem['ptitle'] = $pubtype['description'];
        $pubfilters[] = $pubitem;
    }
    $data['pubfilters'] = $pubfilters;
*/
    $pubtypeobject = DataObjectMaster::getObject(array('name' => 'publications_types'));
    $pubtypeobject->getItem(array('itemid' => $data['ptid']));
    $data['object'] = DataObjectMaster::getObject(array('name' => $pubtypeobject->properties['name']->value));
    $data['properties'] = $data['object']->getProperties();
    $data['items'] = array();

    if (!empty($data['ptid'])) {
        $template = $pubtypeobject->properties['template']->value;
    } else {
// TODO: allow templates per category ?
       $template = null;
    }

    // Get the settings of the publication type we are using
    $data['settings'] = xarModAPIFunc('publications','user','getsettings',array('ptid' => $data['ptid']));
    
    return xarTplModule('publications', 'user', 'new', $data, $template);
}

?>

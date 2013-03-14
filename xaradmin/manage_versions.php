<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

sys::import('modules.dynamicdata.class.objects.master');

function publications_admin_manage_versions($args)
{
    if (!xarSecurityCheck('ManagePublications')) return;
    
    if (!xarVarFetch('itemid',  'id',    $data['page_id'],    0,   XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('name',    'str',   $data['objectname'], '',  XARVAR_NOT_REQUIRED)) {return;}
    if (empty($data['page_id'])) return xarResponse::NotFound();
    
    sys::import('modules.dynamicdata.class.objects.master');
    $entries = DataObjectMaster::getObjectList(array('name' => 'publications_versions'));
    $entries->dataquery->eq('page_id', $data['page_id']);
    $data['versions'] = $entries->countItems() + 1;
    
    if ($data['versions'] < 2) return $data;
    
    if (!xarVarFetch('version_1',  'int',    $version_1, $data['versions'],  XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('version_2',  'int',    $version_2, $data['versions']-1,  XARVAR_NOT_REQUIRED)) {return;}
    $data['version_1'] = $version_1;
    $data['version_2'] = $version_2;
        
    // Assemple options for the version dropdowns
    $data['options'] = array();
    for ($i=$data['versions'];$i>=1;$i--) 
        $data['options'][] = array('id' => $i, 'name' => $i);
        
    // Get an empty object for the page data
    $page = DataObjectMaster::getObject(array('name' => $data['objectname']));

    $version = DataObjectMaster::getObjectList(array('name' => 'publications_versions'));

    if ($data['version_1'] == $data['versions']) {
        $page->getItem(array('itemid' => $data['page_id']));
        $content_array_1 = $page->getFieldValues(array(), 1);
    } else {
        $version->dataquery->eq('page_id', $data['page_id']);
        $version->dataquery->eq('version_number', $version_1);
        $items = $version->getItems();
        if (count($items) > 1)
            throw new Exception(xarML('More than one instance with the version number #(1)', $version_1));
        $item = current($items);
        $content_array_1 = unserialize($item['content']);
    }

    if ($data['version_2'] == $data['versions']) {
        $page->getItem(array('itemid' => $data['page_id']));
        $content_array_2 = $page->getFieldValues(array(), 1);
    } else {
        $version->dataquery->clearconditions();
        $version->dataquery->eq('page_id', $data['page_id']);
        $version->dataquery->eq('version_number', $version_2);
        $items = $version->getItems();
        if (count($items) > 1)
            throw new Exception(xarML('More than one instance with the version number #(1)', $version_2));
        $item = current($items);
        $content_array_2 = unserialize($item['content']);
    }

    $page->tplmodule = 'publications';
    $page->layout = 'publications_documents';

    // Now in turn get the actual display
    $page->setFieldValues($content_array_1, 1);
    $content_1 = $page->showDisplay();
    $page->setFieldValues($content_array_2, 1);
    $content_2 = $page->showDisplay();
    
    // Keep a copy to show if the two versions are identical
    $data['content'] = $content_2;
/*
	sys::import('modules.publications.class.difflib');
	sys::import('modules.publications.class.showdiff');

	$diff = new Diff( explode("\n",$orig_str), explode("\n",$final_str));	
	$objshowdiff = new showdiff();
	$data['result'] = $objshowdiff->checkdiff($orig_str,$final_str,$diff,'Line');
	
	$string_arr= explode("<br>",$data['result']);
*/	
	sys::import('modules.publications.class.lib.Diff');
	sys::import('modules.publications.class.lib.Diff.Renderer.Html.Inline');

    // Explode the content by lines
    $content_1 = explode("\n", $content_1);
    $content_2 = explode("\n", $content_2);

    // Options for generating the diff
    $options = array(
        //'ignoreWhitespace' => true,
        //'ignoreCase' => true,
    );

		// Initialize the diff class
		$diff = new Diff($content_1, $content_2, $options);
		$renderer = new Diff_Renderer_Html_Inline;
		$data['diffresult'] = $diff->render($renderer);



//	$data['content_1'] = nl2br($string_arr[0]);
//	$data['content_2'] = nl2br($string_arr[1]);
    return $data;
}

?>
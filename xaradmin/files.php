<?php
/**
 * View items
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage downloads
 * @link http://www.xaraya.com/index.php/release/19741.html
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * view items
 */
function downloads_admin_files()
{
    if(!xarVarFetch('startnum', 'isset', $startnum, NULL, XARVAR_DONT_SET)) {return;}
	if(!xarVarFetch('sort', 'str', $sort, 'dir', XARVAR_NOT_REQUIRED)) {return;}
	if(!xarVarFetch('numitems', 'int',   $numitems,  NULL, XARVAR_DONT_SET)) {return;}
	if(!xarVarFetch('filter', 'str', $filter, NULL, XARVAR_NOT_REQUIRED)) {return;}
	if(!xarVarFetch('filterfield', 'str', $filterfield, NULL, XARVAR_NOT_REQUIRED)) {return;}
	if(!xarVarFetch('msg', 'str', $data['msg'], NULL, XARVAR_NOT_REQUIRED)) {return;}

	$data['filter'] = $filter;

	if (!xarSecurityCheck('EditDownloads',1)) {
		return;
	}

	sys::import('modules.dynamicdata.class.objects.master');
	sys::import('modules.dynamicdata.class.properties.master');

	$directories = xarMod::apiFunc('downloads','user','getdirectories');

	$locfilter = '';
	$filefilter = '';

	if (isset($filter)) {
		if ($filterfield == 'locfilter') {
			$locfilter = $filter;
		}
		if ($filterfield == 'filefilter') {
			$filefilter = $filter;
		}
	}

	$data['locstartval'] = 'Directory';
	$data['filestartval'] = 'Filename';

	$basepath = xarMod::apiFunc('downloads','admin','getbasepath');
	

	$unfiltered = xarMod::apiFunc('downloads','admin','viewfiles', array('basepath' => $basepath, 'directories' => $directories, 'sort' => $sort));

	if ($locfilter != $data['locstartval'] && $filefilter != $data['filestartval']) {
		$files = xarMod::apiFunc('downloads','admin','viewfiles', array('basepath' => $basepath, 'directories' => $directories, 'locfilter' => $locfilter, 'filefilter' => $filefilter, 'sort' => $sort));
	} else {
		$files = $unfiltered;
	}

	if (is_array($unfiltered)) {
		$data['filtered'] = 1;
		foreach($unfiltered as $fileinfo) {
			$fcount[] = count($fileinfo);	
		} 
		$fcount = array_sum($fcount);
	} else {
		$fcount = 0;
	}

	if (xarModIsAvailable('filters') && xarModVars::get('downloads','enable_filters') && $fcount >= xarModVars::get('downloads','filters_files_min_item_count')) {
		$data['showfilters'] = true;
	} else {
		$data['showfilters'] = false;
	}

	$data['files'] = array();
	if ($files) {
		$data['files'] = $files;
	}
 
    // Return the template variables defined in this function
    return $data;
}

?>

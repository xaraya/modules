<?php

function downloads_userapi_makefilter($args) {

	extract($args);

	if(!xarVarFetch('filter', 'str', $filter, NULL, XARVAR_NOT_REQUIRED)) {return;}
	if(!xarVarFetch('filterfield', 'str', $filterfield, NULL, XARVAR_NOT_REQUIRED)) {return;}

	if(isset($filter) && $filter != $flabel && $thisfield == $filterfield) {
		$icon = 'none.png';
		$filterval = $filter;
	} else {
		$icon = 'blank.png';
		$filterval = $flabel;
	}

	$data['filter'] = $filterval;
	$data['filterfield'] = $thisfield;
	$data['icon'] = $icon;
	$data['flabel'] = $flabel;

	if (isset($ftype)) {
		$data['ftype'] = $ftype;
	}
	if (isset($ffunc)) {
		$data['ffunc'] = $ffunc;
	}
		
	return xarTplModule('downloads','gui','filter', $data);
}

?>
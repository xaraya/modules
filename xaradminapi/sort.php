<?php
/**
 * Sorting
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Contact Form Module
 * @link http://xaraya.com/index.php/release/66.html
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * Sorting
 *
 * @author Ryan Walker
 * @return a string like 'id ASC';
 */
function contactform_adminapi_sort($args)
{
	// Default URL strings to look for
	$url_sortfield = 'sortfield';
	$url_ascdesc = 'ascdesc';

	extract($args);

	if(!xarVarFetch($url_sortfield,     'isset', $sortfield,     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch($url_ascdesc, 'isset', $ascdesc, NULL, XARVAR_NOT_REQUIRED)) {return;}
	
	if (!isset($sortfield)) {
        $sortfield = $sortfield_fallback;
    }

    if (!isset($ascdesc)) {
        $ascdesc = $ascdesc_fallback;
    }

	$sort = $sortfield . ' ' . $ascdesc;

	return $sort;
}

?>
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
 * get an array of publications (id => field) for use in dropdown lists
 *
 * E.g. to specify the parent of an article for parent-child relationships,
 * add a dynamic data field of type Dropdown List with the validation rule
 * xarModAPIFunc('publications','user','dropdownlist',array('ptid' => 1))
 *
 * Note : for additional optional parameters, see the getall() function
 *
 * @param $args['ptid'] publication type ID (for news, sections, reviews, ...)
 * @param $args['field'] field to use in the dropdown list (default 'title')
 * @param $args['showunpub'] (= 1) allow non-admin to see unpublished publications
 * @returns array
 * @return array of publications, or false on failure
 */
function publications_userapi_dropdownlist($args)
{
    if (!isset($args['ptid'])) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'publication type', 'user', 'dropdownlist',
                    'Publications');
        throw new BadParameterException(null,$msg);
    }
    // Add default arguments
    if (!isset($args['field'])) {
        $args['field'] = 'title';
    }
    if (!isset($args['fields'])) {
        $args['fields'] = array('id', $args['field'], 'cids');
    }
    if (!isset($args['sort'])) {
        $args['sort'] = $args['field'];
    }
    // Don't let users see unpublished publications, unless $showunpub is 1
    if ( xarSecurityCheck('AdminPublications',0) ||
        (isset($args['showunpub']) && ($args['showunpub']=='1')) ) {
        $isadmin = true;
    } else {
        $isadmin = false;
    }
    if (!isset($args['state']) || !$isadmin) {
        $args['state'] = array(2, 3);
    }
    if (!isset($args['enddate']) || !$isadmin) {
        $args['enddate'] = time();
    }

    // Get the publications
    $publications = xarModAPIFunc('publications','user','getall',$args);
    if (!$publications) return;

    // Fill in the dropdown list
    $list = array();
    $list[0] = '';
    $field = $args['field'];
    foreach ($publications as $article) {
        if (!isset($article[$field])) continue;
    // TODO: support other formatting options here depending on the field type ?
        $list[$article['id']] = xarVarPrepForDisplay($article[$field]);
    }

    return $list;
}

?>

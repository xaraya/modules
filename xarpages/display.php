<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author St.Ego
 */
function xproject_pages_display($args)
{
    extract($args);
    if (!xarVarFetch('pageid', 'id', $pageid)) return;

    if (!xarModAPILoad('xproject', 'user')) return;
    if (!xarModLoad('addressbook', 'user')) return;

    $data = xarModAPIFunc('xproject','admin','menu');
    $data['pageid'] = $pageid;

    $item = xarModAPIFunc('xproject',
                          'pages',
                          'get',
                          array('pageid' => $pageid));

    if (!isset($item)) return;

    $projectinfo = xarModAPIFunc('xproject',
                          'user',
                          'get',
                          array('projectid' => $item['projectid']));

    list($item['page_name']) = xarModCallHooks('item',
                                         'transform',
                                         $item['pageid'],
                                         array($item['page_name']));

    $childpages = xarModAPIFunc('xproject',
                          'pages',
                          'getall',
                          array('parentid' => $pageid));

    $data['pages_formclick'] = "onClick=\"return loadContent(this.href,'pages_form');\"";

    $data['item'] = $item;
    $data['projectid'] = $item['projectid'];
    $data['projectinfo'] = $projectinfo;
    $data['projectpages'] = $childpages;
    $data['authid'] = xarSecGenAuthKey();
    $data['page_name'] = $item['page_name'];

    return $data;
}
?>

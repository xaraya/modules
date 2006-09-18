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
function xproject_pages_view($args)
{
    extract($args);
    if (!xarVarFetch('projectid', 'id', $projectid)) return;

    if (!xarModAPILoad('xproject', 'admin')) return;

    $data = xarModAPIFunc('xproject','admin','menu');
    $data['projectid'] = $projectid;

    $projectpages = xarModAPIFunc('xproject',
                          'pages',
                          'getall',
                          array('projectid' => $projectid));

    if (!isset($projectpages)) return;

    $data['pages_formclick'] = "onClick=\"return loadContent(this.href,'pages_form');\"";

    $data['projectpages'] = $projectpages;
    $data['authid'] = xarSecGenAuthKey();

    return $data;
}
?>
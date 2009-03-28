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
 * view statistics
 */
function publications_admin_stats($args = array())
{
    if (!xarSecurityCheck('AdminPublications')) return;
    if (!xarVarFetch('group','isset',$group,array(),XARVAR_NOT_REQUIRED)) return;
    extract($args);

    if (!empty($group)) {
        $newgroup = array();
        foreach ($group as $field) {
            if (empty($field)) continue;
            $newgroup[] = $field;
        }
        $group = $newgroup;
    }
    if (empty($group)) {
        $group = array('pubtype_id', 'state', 'owner');
    }

    $data = array();
    $data['group'] = $group;
    $data['stats'] = xarModAPIFunc('publications','admin','getstats',
                                   array('group' => $group));
    $data['pubtypes'] = xarModAPIFunc('publications','user','get_pubtypes');
    $data['statelist'] = xarModAPIFunc('publications','user','getstates');
    $data['fields'] = array('pubtype_id'     => xarML('Publication Type'),
                            'state'        => xarML('Status'),
                            'owner'      => xarML('Author'),
                            'pubdate_year'  => xarML('Publication Year'),
                            'pubdate_month' => xarML('Publication Month'),
                            'pubdate_day'   => xarML('Publication Day'),
                            'locale'      => xarML('Language'));
    return $data;
}

?>

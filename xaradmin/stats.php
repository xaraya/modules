<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author mikespub
 */
/**
 * view statistics
 */
function publications_admin_stats($args = array())
{
    if (!xarSecurity::check('AdminPublications')) return;
    
    if (!xarVar::fetch('group','isset',$group,array(),xarVar::NOT_REQUIRED)) return;
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
    $data['stats'] = xarMod::apiFunc('publications','admin','getstats',
                                   array('group' => $group));
    $data['pubtypes'] = xarMod::apiFunc('publications','user','get_pubtypes');
    $data['statelist'] = xarMod::apiFunc('publications','user','getstates');
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
<?php
/**
 * Articles module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 */
/**
 * view statistics
 */
function articles_admin_stats($args = array())
{
    if (!xarSecurityCheck('AdminArticles')) return;
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
        $group = array('pubtypeid', 'status', 'authorid');
    }

    $data = array();
    $data['group'] = $group;
    $data['stats'] = xarModAPIFunc('articles','admin','getstats',
                                   array('group' => $group));
    $data['pubtypes'] = xarModAPIFunc('articles','user','getpubtypes');
    $data['statuslist'] = xarModAPIFunc('articles','user','getstates');
    $data['fields'] = array('pubtypeid'     => xarML('Publication Type'),
                            'status'        => xarML('Status'),
                            'authorid'      => xarML('Author'),
                            'pubdate_year'  => xarML('Publication Year'),
                            'pubdate_month' => xarML('Publication Month'),
                            'pubdate_day'   => xarML('Publication Day'),
                            'language'      => xarML('Language'));
    return $data;
}

?>

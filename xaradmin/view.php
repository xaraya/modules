<?php
/**
 * Comments Module
 *
 * @package modules
 * @subpackage comments
 * @category Third Party Xaraya Module
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
sys::import('modules.comments.xarincludes.defines');
/**
 * This is a standard function to modify the configuration parameters of the
 * module
 * @return array
 */
function comments_admin_view()
{
    if (!xarSecurity::check('ManageComments')) {
        return;
    }

    // Only show top level documents, not translations
    sys::import('xaraya.structures.query');
    $q = new Query();
    $q->ne('status', _COM_STATUS_ROOT_NODE);

    // Suppress deleted items if not an admin
    // Remove this once listing property works with dataobject access
    if (!xarRoles::isParent('Administrators', xarUser::getVar('uname'))) {
        $q->ne('status', 0);
    }
    $data['conditions'] = $q;
    return $data;

    if (!xarVar::fetch('startnum', 'int', $startnum, 1, xarVar::NOT_REQUIRED)) {
        return;
    }

    $sort = xarMod::apiFunc('comments', 'admin', 'sort', [
        //how to sort if the URL or config say otherwise...
        'sortfield_fallback' => 'date',
        'ascdesc_fallback' => 'DESC',
    ]);
    $data['sort'] = $sort;

    $object = DataObjectMaster::getObject(['name' => 'comments_comments']);
    $config = $object->configuration;
    $adminfields = reset($config['adminfields']);
    $numitems = xarModVars::get('comments', 'items_per_page');

    $filters = [];

    // Total number of comments for use in the pager
    $total = DataObjectMaster::getObjectList([
                            'name' => 'comments_comments',
                            'numitems' => null,
                            'where' => 'status ne ' . _COM_STATUS_ROOT_NODE,
                            ]);
    $data['total'] = $total->countItems();

    $filters_min_items = xarModVars::get('comments', 'filters_min_item_count');

    $data['makefilters'] = [];
    $data['showfilters'] = false;

    if (xarMod::isAvailable('filters') && xarModVars::get('comments', 'enable_filters') && $data['total'] >= $filters_min_items) {
        $data['showfilters'] = true;
        $filterfields = $config['filterfields'];
        $get_results = xarMod::apiFunc('filters', 'user', 'dd_get_results', [
                            'filterfields' => $filterfields,
                            'object' => 'comments',
                            ]);
        $data = array_merge($data, $get_results);
        if (isset($data['filters'])) {
            $filters = $data['filters'];
        }
    }

    if (isset($filters['where'])) {
        $filters['where'] .=  ' and ';
    } else {
        $filters['where'] = '';
    }

    $filters['where'] .= 'status ne ' . _COM_STATUS_ROOT_NODE;

    $list = DataObjectMaster::getObjectList([
                            'name' => 'comments_comments',
                            'sort' => $sort,
                            'startnum' => $startnum,
                            'numitems' => $numitems,
                            'fieldlist' => $adminfields,
        ]);

    if (!is_object($list)) {
        return;
    }

    $list->getItems($filters);

    $data['list'] = $list;

    return $data;
}

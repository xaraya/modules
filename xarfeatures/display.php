<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author St.Ego
 */
function xproject_features_display($args)
{
    extract($args);
    if (!xarVarFetch('featureid', 'id', $featureid)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $featureid = $objectid;
    }

    $data = xarModAPIFunc('xproject','user','menu');
    $data['featureid'] = $featureid;
    $data['status'] = '';

    $item = xarModAPIFunc('xproject',
                          'features',
                          'get',
                          array('featureid' => $featureid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    list($item['feature_name']) = xarModCallHooks('item',
                                         'transform',
                                         $item['featureid'],
                                         array($item['feature_name']));

    $data['feature_name'] = $item['feature_name'];
    $data['item'] = $item;

    return $data;
}
?>

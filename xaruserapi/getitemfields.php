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
 * utility function to pass item field definitions to whoever
 *
 * @param $args['itemtype'] item type (optional)
 * @return array Array containing the item field definitions
 */
function publications_userapi_getitemfields($args)
{
    extract($args);

    $itemfields = array();

    $pubtypes = xarModAPIFunc('publications','user','get_pubtypes');

    if (!empty($itemtype) && !empty($pubtypes[$itemtype])) {
        $fields = $pubtypes[$itemtype]['config'];
    } else {
        $fields = xarModAPIFunc('publications','user','getpubfields');
    }
    foreach ($fields as $name => $info) {
        if (empty($info['label'])) continue;
        $itemfields[$name] = array('name'  => $name,
                                   'label' => $info['label'],
                                   'type'  => $info['format']);
    }

    return $itemfields;
}

?>

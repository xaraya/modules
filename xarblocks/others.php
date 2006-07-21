<?php
/**
 * Other block initialization
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */

/**
 * initialise block
 *
 * @author Example Module development team
 * @return array
 */
function example_othersblock_init()
{
    return array(
        'numitems' => 5
    );
}

/**
 * get information on block
 */
function example_othersblock_info()
{
    /* Values */
    return array(
        'text_type' => 'Others',
        'module' => 'example',
        'text_type_long' => 'Show other example items when 1 is displayed',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true
    );
}

/**
 * display block
 * @return array
 */
function example_othersblock_display($blockinfo)
{
    /* See if we are currently displaying an example item
     * (this variable is set in the user display function)
     */
    if (!xarVarIsCached('Blocks.example', 'exid')) {
        // if not, we don't show this
        return;
    }

    $current_exid = xarVarGetCached('Blocks.example', 'exid');
    if (empty($current_exid) || !is_numeric($current_exid)) {
        return;
    }

    /* Security check */
    if (!xarSecurityCheck('ReadExampleBlock', 0, 'Block', $blockinfo['name'])) {return;}

    /* Get variables from content block.
     * Content is a serialized array for legacy support, but will be
     * an array (not serialized) once all blocks have been converted.
     */
    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    /* Defaults */
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    }

    /* Database information */
    xarModDBInfoLoad('example');
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $exampletable = $xartable['example'];

    /* Query */
    $sql = "SELECT xar_exid, xar_name
            FROM $exampletable
            WHERE xar_exid != $current_exid
            ORDER by xar_exid DESC";
    $result = $dbconn->SelectLimit($sql, $vars['numitems']);

    if ($dbconn->ErrorNo() != 0) {
        return;
    }

    if ($result->EOF) {
        return;
    }

    /* Create output object */
    $items = array();

    /* Display each item, permissions permitting */
    for (; !$result->EOF; $result->MoveNext()) {
        list($exid, $name) = $result->fields;

        if (xarSecurityCheck('ViewExample', 0, 'Item', "$name:All:$exid")) {
            if (xarSecurityCheck('ReadExample', 0, 'Item', "$name:All:$exid")) {
                $item = array();
                $item['link'] = xarModURL(
                    'example', 'user', 'display',
                    array('exid' => $exid)
                );

            }
            $item['name'] = $name;
        }
        $items[] = $item;
    }

    $blockinfo['content'] = array('items' => $items);

    return $blockinfo;
}
?>

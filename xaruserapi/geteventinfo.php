<?php
/**
 * Get information on a linked event
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */
/**
 * Get information on linked event.
 *
 * This function uses the general module information to get links to hooked events
 *
 * @author Jorn, MichelV <michelv@xaraya.com>
 *
 * @param id  iid id of hooked item (in hooking module, e.g. an article id)
 * @param itemtype: type of hooked item
 * @param modid:    id of hooking module
 * @param event:    current event data
 */
function julian_userapi_geteventinfo($args)
{
    extract($args);

    if (!isset($iid) || !is_numeric($iid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'user', 'geteventinfo', 'Julian');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    $modinfo = xarModGetInfo($modid);
    $modname = $modinfo['name'];

    /*
     * Get the event via getitemlinks
     * @param $args['itemtype'] item type (optional)
     * @param $args['itemids'] array of item ids to get
     * @param $args['field'] field to return as label in the list (default 'title')

        $itemlinks[$itemid] = array('url'   => xarModURL('articles', 'user', 'display',
                                                                 array('ptid' => $article['pubtypeid'],
                                                                       'aid' => $article['aid'])),
                                            'title' => xarML('Display Article'),
                                            'label' => xarVarPrepForDisplay($article[$field]));

     */
    $event =array();
    $event['viewUrl']='';
    $event['summary']='';
    $event['description'] = '';
   // $event['artstatus'] = 1;
    $field = 'title';
    $item = xarModApiFunc($modname,'user','getitemlinks',array('itemids'=> array($iid),'field'=> $field));
    // Check the output
    if (empty($item[$iid]['url'])) {
        return array();
    } else {
        $event['viewURL'] = $item[$iid]['url'];
        $event['summary'] = $item[$iid]['title'];
        if (!empty($item[$iid]['label'])) {
            $event['description'] = $item[$iid]['label'];
        } else {
            $event['description'] = xarML('No description entered');
        }
    }

    return $event;
}
?>

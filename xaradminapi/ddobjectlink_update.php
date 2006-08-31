<?php
/**
 * Subitems module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Subitems Module
 * @link http://xaraya.com/index.php/release/9356.html
 * @author Subitems Module Development Team
 */
/**
 * Update a dd link
 * @param $args['objectid']
 * @return bool
 */
function subitems_adminapi_ddobjectlink_update($args)
{
    // Get arguments from argument array
    extract($args);

    if(!isset($objectid))
        $invalid[] = "objectid";

    if(isset($sort))    {
        if(!is_array($sort))
            $invalid[] = "sort";
        else
            $sort = @serialize($sort);
    }

    // params in arg
    $params = array("template" => "xar_template",
                    "itemtype" => "xar_itemtype",
                    "module" => "xar_module",
                    "sort" => "xar_sort");
    foreach($params as $vvar => $dummy)    {
        if(isset($$vvar))    {
            $set = true;
            break;
        }
    }
    if(    !isset($set)   )
        $invalid[] = "at least one of these has to be set: ".join(",",array_keys($fields));

    // Argument check - make sure that at least on parameter is present
    // if not then set an appropriate error message and return
    if ( isset($invalid) ) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'admin', 'ddobjectlink_update', 'subitems');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }


    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // now
    $time = date('Y-m-d G:i:s');

    if (!isset($bindvars)) {
        $bindvars = array();
    }

    foreach($params as $vvar => $field)    {
        if(isset($$vvar)) {
            $update[] = $field ." = ?";
            if ('itemtype' == $vvar) {
                $bindvars[] = (int) $$vvar;
            } else {
                $bindvars[] = (string) $$vvar;
            }
        }
    }

    // Update item
    $query = "UPDATE {$xartable['subitems_ddobjects']} SET ".join(",",$update)." WHERE xar_objectid = ?";
    $bindvars[] = (int) $objectid;

    $result =& $dbconn->Execute($query, $bindvars);
    if (!$result) return;

    // Let any hooks know that we have updated a new topic
    $args['module'] = 'subitems';
    $args['itemtype'] = 1; // topic
    $args['itemid'] = $objectid;
    xarModCallHooks('item', 'modify', $objectid, $args);

    // Return the id of the newly created link to the calling process
    return true;
}

?>

<?php
/*
 * Create a page type.
 * It is added to the group hierarchy.
 * Mandatory columns:
 *  name: group name
 *  desc: group description
 *  insertpoint: ID of group inserting relative to
 *  offset: relationship to insertpoint ('after', 'before', 'firstchild', 'lastchild')
 * TODO: allow explicit DD fields to be passed into this API
 * TODO: check the page type is valid
 * TODO: default most values and raise an error in missing mandatory values
 */

function xarpages_adminapi_createtype($args)
{
    extract($args);

    // Security: allowed to create page types?
    if ($name[0] <> '@') {
        if (!xarSecurityCheck('AdminXarpagesPagetype', 1, 'Pagetype', 'All')) {
            return;
        }
    }

    // Get the pagetype itemtype ID. The first time this is ever called,
    // the system itemtype pagetype will be created, so do it first to
    // increase the likelyhood that it will get ID number 1.
    if ($name[0] != '@') {
        $type_itemtype = xarModAPIfunc('xarpages', 'user', 'gettypeitemtype');
    }

    // TODO: validate name (mandatory and unique)

    $xartable =& xarDBGetTables();
    $dbconn =& xarDBGetConn();

    $tablename = $xartable['xarpages_types'];

    // Data for the query.
    $bind = array();
    $cols = array();

    // Include the optional parameters.
    foreach(array('desc') as $colname) {
        if (isset($$colname)) {
            $bind[] = (string)$$colname;
            $cols[] = 'xar_' . $colname;
        }
    }

    $bind[] = $name;
    $cols[] = 'xar_name';

    // Insert the page
    $nextID = $dbconn->GenId($tablename);
    $bind[] = $nextID;
    $cols[] = 'xar_ptid';

    $query = 'INSERT INTO ' . $tablename
        . '(' .implode(', ', $cols). ')'
        . ' VALUES(?' . str_repeat(',?', count($cols)-1) . ')';

    $result = $dbconn->execute($query, $bind);
    if (!$result) {return;}

    $ptid = $dbconn->PO_Insert_ID($tablename, 'xar_ptid');

    // If there is a suitable XML definition file available, then
    // use it to create the DD hook object.
    $files = array();
    $basedir = 'modules/xarpages/xardata';
    $xml_files = xarModAPIFunc(
        'dynamicdata', 'admin', 'browse',
        array('basedir' => $basedir, 'filetype' => 'xml')
    );
    if (!empty($xml_files)) {
        foreach($xml_files as $xml_file) {
            $type_name = preg_replace('/-def\.xml$/', '', $xml_file);
            $files[$type_name] = $xml_file;
        }

        if (isset($files[$name])) {
            // There is an XML file to import.
            $objectid = xarModAPIFunc(
                'dynamicdata', 'util', 'import',
                array('file' => $basedir . '/' . $files[$name], 'keepitemid' => false)
            );

            // If the object was created correctly, then update its itemtype.
            // We also need to change the itemtype of all its properties.
            if (!empty($objectid)) {
                Dynamic_Object_Master::updateObject(
                    array('objectid' => $objectid, 'itemtype' => $ptid)
                );

                // Synchronise the object properties with the change in itemtype.
                // TODO: either this needs to be done automatically when the object is
                // updated, or the need for keeping an itemtype on the properties should
                // be removed.
                xarModAPIFunc('dynamicdata', 'admin', 'syncprops',
                    array(
                        'objectid' => $objectid,
                        'moduleid' => xarModGetIDFromName('xarpages'),
                        'itemtype' => $ptid
                    )
                );
            }
        }
    }

    // Hooks: we have created an instance of the 'page type' type.

    // Get the itemtype of the page type.
    // Only do this for the non-system page types. The 'system' page types
    // are just placeholders for various type IDs. They are created as
    // substantive rows to ensure a complete set of unique itemtype IDs
    // across the whole module.
    if (!empty($type_itemtype)) {
        // Create hooks.
        xarModCallHooks(
            'item', 'create', $ptid,
            array(
                'itemtype' => $type_itemtype,
                'module' => 'xarpages',
                'urlparam' => 'ptid'
            )
        );
    }

    return $ptid;
}

?>
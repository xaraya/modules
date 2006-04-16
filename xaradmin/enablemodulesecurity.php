<?php
/**
 * Security - Provides unix style privileges to xaraya items.
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Security Module
 * @author Brian McGilligan <brian@mcgilligan.us>
 */
/**
    Creates security levels for module items that already exist
    (Useful if a bunch of items are created before the security
     module is hooked in.  Usefull in upgrades. )

    @return boolean true on success otherwise false
*/
function security_admin_enablemodulesecurity($args)
{
    if( !xarSecurityCheck('AdminSecurity') ) return false;

    xarModAPILoad('Security');
    $default_user_level  = SECURITY_OVERVIEW+SECURITY_READ+SECURITY_COMMENT+SECURITY_WRITE+SECURITY_ADMIN;
    $default_group_level  = SECURITY_OVERVIEW+SECURITY_READ;
    $default_world_level  = SECURITY_OVERVIEW+SECURITY_READ;

    xarVarFetch('mod',        'id',  $modid,     xarModGetIdFromName('categories'), XARVAR_NOT_REQUIRED);
    xarVarFetch('table',      'str', $table,     '', XARVAR_NOT_REQUIRED);
    xarVarFetch('itemtype',   'int', $itemtype,   0,    XARVAR_NOT_REQUIRED);

    xarVarFetch('user_level', 'int', $user_level, $default_user_level, XARVAR_NOT_REQUIRED);
    xarVarFetch('group_level','int', $group_level,$default_group_level, XARVAR_NOT_REQUIRED);
    xarVarFetch('world_level','int', $world_level,$default_world_level, XARVAR_NOT_REQUIRED);
    xarVarFetch('uid',        'str', $uid, xarUserGetVar('uid'));
    xarVarFetch('group',      'int', $gid,         null, XARVAR_NOT_REQUIRED);
    xarVarFetch('submit',     'str', $submit,      null, XARVAR_NOT_REQUIRED);

    /*
        Setup a Datadict object
    */
    $dbconn =& xarDBGetConn();
    $dict   =& xarDBNewDataDict($dbconn);

    if( $submit )
    {
        xarModLoad('owner', 'user');

        /*
            Get and examine columns to try and find the primary key or id for the item
        */
        $cols = $dict->getColumns($table);
        foreach( $cols as $col )
        {
            if( $col->primary_key && substr_count($col->type, 'int') > 0 )
            {
                $itemIdCol = $col->name;
                break;
            }
        }

        if( !isset($itemIdCol) )
        {
            $msg = 'Error! Could not find the primary key';
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST', $msg);
            return false;
        }

        if( empty($uid) ){ $uid = xarUserGetVar('uid'); }

        // Now get all ids from DB
        $query = "SELECT $itemIdCol, $uid FROM $table ";
        $rows = $dbconn->Execute($query);

        while( (list($itemid, $uid) = $rows->fields) != null )
        {
            $secArgs = array(
                'modid'      => $modid,
                'itemtype'   => $itemtype,
                'itemid'     => $itemid,
                'uid'        => $uid,
                'settings'   => array(
                    'levels' => array(
                        'user'   => $user_level,
                        'world'  => $world_level,
                        'groups' => array(
                            $gid => $group_level
                        )
                    )
                )
            );

            $exists = xarModAPIFunc('security', 'user', 'securityexists', $secArgs);
            if( !$exists )
                xarModAPIFunc('security', 'admin', 'create', $secArgs);

            $exists = xarModAPIFunc('owner', 'user', 'ownerexists', $secArgs);
            if( !$exists )
                xarModAPIFunc('owner', 'admin', 'create', $secArgs);

            $rows->MoveNext();
        }
    }

    extract($args);

    $data = array();

    $data['modid'] = $modid;
    $data['gid'] = $gid;
    $data['tables']  = $dict->getTables();
    $data['user_level']  = $user_level;
    $data['group_level'] = $group_level;
    $data['world_level'] = $world_level;

    return $data;
}
?>
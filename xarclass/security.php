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
 * Main class for interfacing with the security module.
 *
 */
class Security
{
    /**
     * Check if a user has the needed security levels to access the given module
     * or item.
     *
     * @param integer $needed_level
     * @param integer $modid
     * @param integer $itemtype
     * @param integer $itemid
     * @param boolean $throw_exception
     * @return boolean
     */
    function check($needed_level, $modid=0, $itemtype=0, $itemid=0, $throw_exception=true)
    {
        // Security admin also has access to eveything.
        if( $modid != 'security' and Security::check(SECURITY_ADMIN, 'security', 0, 0, false) ){ return true; }

        if( is_string($modid) ){ $modid = xarModGetIdFromName($modid); }

        $cache_name = "security.$needed_level.$modid.$itemtype.$itemid";
        if( xarVarIsCached('modules.security', $cache_name) )
        {
            return (boolean) xarVarGetCached('modules.security', $cache_name);
        }

        // Get Module Settings
        $settings = SecuritySettings::factory($modid, $itemtype);

        // Get DB conn ready
        $dbconn =& xarDBGetConn();
        $xartable =& xarDBGetTables();

        $secRolesTable = $xartable['security_roles'];

        $bindvars = array();
        $where = array();

        switch( $needed_level )
        {
            case SECURITY_ADMIN:
                $field = 'xadmin';
                break;
            case SECURITY_MANAGE:
                $field = 'xmanage';
                break;
            case SECURITY_WRITE:
                $field = 'xwrite';
                break;
            case SECURITY_COMMENT:
                $field = 'xcomment';
                break;
            case SECURITY_READ:
                $field = 'xread';
                break;
            case SECURITY_OVERVIEW:
                $field = 'xoverview';
                break;
            default:
                $field = 'xread';
        }

        $query = "
            SELECT $field
            FROM $secRolesTable
        ";

        $where[] = "$secRolesTable.modid IN (0, ?) ";
        $bindvars[] = (int)$modid;
        $where[] = "$secRolesTable.itemtype IN (0, ?) ";
        $bindvars[] = (int)$itemtype;
        $where[] = "$secRolesTable.itemid = ?";
        $bindvars[] = (int)$itemid;

        //Check Groups
        $uids = array(0, xarUserGetVar('uid'));
        $roles = new xarRoles();
        $user = $roles->getRole(xarUserGetVar('uid'));
        $tmp = $user->getParents();
        foreach( $tmp as $u )
        {
            $uids[] = $u->uid;
        }
        $where[] = "uid IN (". join(', ', $uids) .")";


        // Check for world
        $where[] = "$field = 1";

        if( count($where) > 0 )
        {
            $query .= ' WHERE ' . join(' AND ', $where);
        }

        $result = $dbconn->Execute($query, $bindvars);


        // Cache the result for faster lookups later.
        xarVarSetCached('modules.security', $cache_name, !$result->EOF);

        if( $result->EOF )
        {
            if( $throw_exception == true )
            {
                $msg = "You do not have the proper security to perform this action!";
                xarErrorSet(XAR_USER_EXCEPTION, 'NO_PRIVILEGES', $msg);
            }

            return false;
        }

        return true;
    }

    /**
     * Creates the security levels in the database.
     *
     * @param SecurityLevels $levels
     * @param integer $modid
     * @param integer $itemtype
     * @param integer $itemid
     * @return boolean
     */
    function create($levels, $modid=0, $itemtype=0, $itemid=0)
    {
        $dbconn =& xarDBGetConn();
        $xartable =& xarDBGetTables();

        $securityRolesTable = $xartable['security_roles'];

        $query = "DELETE FROM $securityRolesTable ";
        $where = array();
        $bindvars = array();
        $where[] = " modid = ? ";
        $bindvars[] = $modid;
        $where[] = " itemtype = ? ";
        $bindvars[] = $itemtype;
        $where[] = " itemid = ? ";
        $bindvars[] = $itemid;

        if( count($where) > 0 )
        {
            $query .= ' WHERE ' . join(" AND ", $where);
        }
        $result = $dbconn->Execute($query, $bindvars);
        if( !$result ) return false;

        // extract Securty levels as an array
        // would have made SecurityLevels implement the iterator
        // but I don't think it is supported in 4.x
        if( is_object($levels) )
        {
            if( get_class($levels) == 'SecurityLevels' )
            {
                $levels = $levels->levels;
            }
        }

        foreach( $levels as $role_id => $level )
        {
            if( $level->overview > 0 || $level->read > 0   || $level->comment > 0
                || $level->write > 0 || $level->manage > 0 || $level->admin > 0 )
            {
                $query =
                    "INSERT INTO $securityRolesTable "
                    . "(modid, itemtype, itemid, uid, xoverview, xread, xcomment, xwrite, xmanage, xadmin)  "
                    . "VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ) ";

                $bindvars = array(
                    isset($modid)      ? $modid    : 0
                    , isset($itemtype) ? $itemtype : 0
                    , isset($itemid)   ? $itemid   : 0
                    , isset($role_id)  ? $role_id  : 0
                    , !empty($level->overview)? 1 : 0
                    , !empty($level->read)    ? 1 : 0
                    , !empty($level->comment) ? 1 : 0
                    , !empty($level->write)   ? 1 : 0
                    , !empty($level->manage)  ? 1 : 0
                    , !empty($level->admin)   ? 1 : 0
                );
                $result = $dbconn->Execute($query, $bindvars);
                if( !$result ) return false;
            }
        }

        return true;
    }

    /**
     * Generates leftjoin SQL to apply security on groups of items in a database table.
     * Items with out the proper security level with not be retreived from the database.
     *
     * @param integer $level
     * @param integer $modid
     * @param integer $itemtype
     * @param integer $itemid
     * @param integer $user_field
     * @param integer $limit_gids
     * @param array   $exceptions
     * @return array of SQL for a left join
     */
    function leftjoin($level=SECURITY_OVERVIEW, $modid=0, $itemtype=0, $itemid=0, $user_field=null, $limit_gids=null, $exceptions=null)
    {
        $info = array();

        // Get current user and groups
        $currentUserId = xarUserGetVar('uid');
        $groups = array();

        $xartable =& xarDBGetTables();

        $info['iid'] = "{$xartable['security']}.itemid";

        $secRolesTable = $xartable['security_roles'];

        $where = array();
        $join = array();
        $where[] = "$secRolesTable.modid = $modid ";
        $where[] = "$secRolesTable.itemtype = $itemtype ";
        if( is_array($itemid) )
        {
            $where[] = "$secRolesTable.itemid IN ( " . join(', ', $iids) . " )";
        }
        else
        {
            $where[] = "$secRolesTable.itemid = $itemid";
        }

        //Check Groups
        if( isset($limit_gids) and count($limit_gids) > 0 )
        {
            $uids = $limit_gids;
        }
        else
        {
            $roles = new xarRoles();
            $user = $roles->getRole($currentUserId);
            $tmp = $user->getParents();
            $uids = array(0, $currentUserId);
            foreach( $tmp as $u )
            {
                $uids[] = $u->uid;
            }
        }
        $where[] = "$secRolesTable.uid IN (". join(', ', $uids) .")  ";

        switch( $level )
        {
            case SECURITY_ADMIN:
                $level = "$secRolesTable.xadmin = 1";
                break;
            case SECURITY_MANAGE:
                $level = "$secRolesTable.xmanage = 1";
                break;
            case SECURITY_WRITE:
                $level = "$secRolesTable.xwrite = 1";
                break;
            case SECURITY_COMMENT:
                $level = "$secRolesTable.xcomment = 1";
                break;
            case SECURITY_READ:
                $level = "$secRolesTable.xread = 1";
                break;
            case SECURITY_OVERVIEW:
                $level = "$secRolesTable.xoverview = 1";
                break;
            default:
                $level = "$secRolesTable.xread = 1";
        }

        /*
            Admin's always have access to everything (A security level bypass)
            NOTE: But this also allows admins to use other limits or
                  exclude params like the $limit_gids var
        */
        if( Security::check(SECURITY_ADMIN, 'security', 0, 0, false) )
        {
            $skip_exceptions = true;
            // Still needed if limit_gids is set
            $exceptions[] = " 'TRUE' = 'TRUE' ";
        }

        if( !empty($exceptions) )
        {
            if( isset($limit_gids) and count($limit_gids) > 0 )
            {
                $where[] = " ( $level OR " . join(' OR ', $exceptions) . ") ";
            }
            else
            {
                 // Admin user and no limit are needed so we do not need to do anything
                 if( isset($skip_exceptions) ){ $where = array(); }
                 else{ $where[] = " $level OR " . join(' OR ', $exceptions) . " "; }
            }
        }
        else
        {
            $where[] = " $level ";
        }


        if( count($where) > 0 )
        {
            $info['where'] = "( SELECT count(*) > 0 FROM {$secRolesTable} "
                . "WHERE "  . join(' AND ', $where) . " )";
        }

        return $info;
    }


    /**
     * Sets the security levels in the database.
     *
     * @param SecurityLevels $levels
     * @param integer $modid
     * @param integer $itemtype
     * @param integer $itemid
     * @return boolean
     */
    function update($levels, $modid=0, $itemtype=0, $itemid=0)
    {
        return Security::create($levels, $modid, $itemtype, $itemid);
    }
}

/**
 * Describes a basic security level.
 *
 */
class SecurityLevel
{
    var $overview = 0;
    var $read     = 0;
    var $comment  = 0;
    var $write    = 0;
    var $manage   = 0;
    var $admin    = 0;

    function SecurityLevel($overview=0, $read=0, $comment=0, $write=0, $manage=0, $admin=0)
    {
        if( is_array($overview) )
        {
            $array_level = $overview;
            foreach( $array_level as $key => $value )
            {
                $this->$key = $value;
            }
        }
        else
        {
            $this->overview = $overview;
            $this->read     = $read;
            $this->comment  = $comment;
            $this->write    = $write;
            $this->manage   = $manage;
            $this->admin    = $admin;
        }
    }

    function clear()
    {
        $this->overview = 0;
        $this->read     = 0;
        $this->comment  = 0;
        $this->write    = 0;
        $this->manage   = 0;
        $this->admin    = 0;
        return true;
    }

    function init()
    {
        return $this->clear();
    }
}

/**
 * A Container class for the SecurityLevel class.  Groups SecurityLevel objects together.
 *
 */
class SecurityLevels
{
    var $modid    = 0;
    var $itemtype = 0;
    var $itemid   = 0;
    var $levels   = array();
    var $user_names = array();

    function SecurityLevels($modid=0, $itemtype=0, $itemid=0)
    {
        $this->modid    = $modid;
        $this->itemtype = $itemtype;
        $this->itemid   = $itemid;
    }

    function add($level, $uid=0, $username='Unknown')
    {
        $this->levels[$uid] = $level;
        $this->user_names[$uid] = $username;
        return true;
    }

    function remove($uid=0)
    {
        unset($this->levels[$uid]);
        return true;
    }

    function clear()
    {
        $this->modid    = 0;
        $this->itemtype = 0;
        $this->itemid   = 0;
        $this->levels   = array();
        return true;
    }

    function init()
    {
        return $this->clear();
    }
}

?>
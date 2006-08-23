<?php

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
        if( is_string($modid) ){ $modid = xarModGetIdFromName($modid); }

        // TODO ADD caching mechanism here
        $cache_name = "security.$modid.$itemtype.$itemid.$needed_level";
        if( xarVarIsCached('modules.security', $cache_name) )
        {
            return xarVarGetCached('modules.security', $cache_name);
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

        $where[] = "$secRolesTable.modid = ?";
        $bindvars[] = (int)$modid;
        $where[] = "$secRolesTable.itemtype = ?";
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

        return true;
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

//    function SecurityLevel($array_level=array())
//    {
//        if( is_array($array_level) )
//        {
//            foreach( $array_level as $key => $value )
//            {
//                $this->$key = $value;
//            }
//        }
//    }

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
<?php

interface IAccess
{
    function     HostRules($user);
    function   ObjectRules($user);
    function IdentityRules($user);
}

interface IAccessRule
{
    function __construct($user);
    function canUse($item);
}

class AccessRules implements IAccess
{
    private $delegate = null; 
    
    public function __construct(&$type)
    {
        // See if we can figure out what the caller wants 
        if($type instanceof SQLiteDatabase)
        {
            php::import('server.accessrules.sqlite');
            $this->delegate = new SQLiteAccessRules($type);
        //} elseif($type instanceof Connection)
        //{
        //    php::import('server.accessrules.creole');
        //    $this->delegate = new CreoleAccessRules($type);
        } else
            throw Exception("Unknown AccessRules type ($type)");
    }
    
    function HostRules($user)
    {
        return $this->delegate->HostRules($user);
    }
    function ObjectRules($user)
    {
        return $this->delegate->ObjectRules($user);
    }
    function IdentityRules($user)
    {
        return $this->delegate->IdentityRules($user);
    }
}
?>

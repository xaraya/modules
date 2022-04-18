<?php

namespace Galaxia\Managers;

include_once(GALAXIA_LIBRARY.'/managers/base.php');

//!! InstanceManager
//! A class to maniplate instances
/*!
  This class is used to add,remove,modify and list
  instances.
*/
class InstanceManager extends BaseManager
{
    public function get_instance_activities($iid)
    {
        $query = "select ga.type,ga.isInteractive,ga.isAutoRouted,gi.pId,ga.activityId,ga.name,gi.instanceId,gi.status,gia.activityId,gia.user,gi.started,gia.status as actstatus " .
             "from ".self::tbl('activities')." ga,".self::tbl('instances')." gi,".self::tbl('instance_activities')." gia ".
             "where ga.activityId=gia.activityId and gi.instanceId=gia.instanceId and gi.instanceId=?";
        $result = $this->query($query, [$iid]);
        $ret = [];
        while ($res = $result->fetchRow()) {
            // Number of active instances
            $ret[] = $res;
        }
        return $ret;
    }

    public function get_instance($iid)
    {
        $query = "select * from ".self::tbl('instances')." gi where instanceId=?";
        $result = $this->query($query, [$iid]);
        $res = $result->fetchRow();
        $res['workitems']=$this->getOne("select count(*) from ".self::tbl('workitems')."where instanceId=?", [$iid]);
        return $res;
    }

    public function get_instance_properties($iid)
    {
        $prop = unserialize($this->getOne("select properties from ".self::tbl('instances')."gi where instanceId=?", [$iid]));
        return $prop;
    }

    public function set_instance_properties($iid, &$prop)
    {
        $props = serialize($prop);
        $query = "update ".self::tbl('instances')." set properties= ? where instanceId= ?";
        $this->query($query, [$props,$iid]);
    }

    public function set_instance_name($iid, $name)
    {
        $query = "update ".self::tbl('instances')." set name= ? where instanceId= ?";
        $this->query($query, [$name,$iid]);
    }

    public function set_instance_owner($iid, $owner)
    {
        $query = "update ".self::tbl('instances')."  set owner=? where instanceId=?";
        $this->query($query, [$owner, $iid]);
    }

    public function set_instance_status($iid, $status)
    {
        $query = "update ".self::tbl('instances')."  set status=? where instanceId=?";
        $this->query($query, [$status, $iid]);
    }

    public function set_instance_destination($iid, $activityId)
    {
        $query = "delete from ".self::tbl('instance_activities')." where instanceId=?";
        $this->query($query, [$iid]);
        $query = "insert into ".self::tbl('instance_activities')." (instanceId,activityId,user,status) values(?,?,?,?)";
        $this->query($query, [$iid, $activityId, '*', 'running']);
    }

    public function set_instance_user($iid, $activityId, $user)
    {
        $query = "update ".self::tbl('instance_activities')." set user=?, status=? where instanceId=? and activityId=?";
        $this->query($query, [$user, 'running', $iid, $activityId]);
    }
}

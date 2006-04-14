<?php
include_once(GALAXIA_LIBRARY.'/src/ProcessManager/BaseManager.php');
//!! InstanceManager
//! A class to maniplate instances
/*!
  This class is used to add,remove,modify and list
  instances.
*/
class InstanceManager extends BaseManager {
  
  function get_instance_activities($iid)
  {
    $query = "select ga.type,ga.isInteractive,ga.isAutoRouted,gi.pId,ga.activityId,ga.name,gi.instanceId,gi.status,gia.activityId,gia.user,gi.started,gia.status as actstatus " .
             "from ".GALAXIA_TABLE_PREFIX."activities ga,".GALAXIA_TABLE_PREFIX."instances gi,".GALAXIA_TABLE_PREFIX."instance_activities gia ".
             "where ga.activityId=gia.activityId and gi.instanceId=gia.instanceId and gi.instanceId=?";
    $result = $this->query($query, array($iid));
    $ret = Array();
    while($res = $result->fetchRow()) {
      // Number of active instances
      $ret[] = $res;
    }
    return $ret;
  }

  function get_instance($iid)
  {
    $query = "select * from ".GALAXIA_TABLE_PREFIX."instances gi where instanceId=?";
    $result = $this->query($query, array($iid));
    $res = $result->fetchRow();
    $res['workitems']=$this->getOne("select count(*) from ".GALAXIA_TABLE_PREFIX."workitems where instanceId=?",array($iid));
    return $res;
  }

  function get_instance_properties($iid)
  {
      $prop = unserialize($this->getOne("select properties from ".GALAXIA_TABLE_PREFIX."instances gi where instanceId=?",array($iid)));
    return $prop;
  }
  
  function set_instance_properties($iid,&$prop)
  {
    $props = serialize($prop);
    $query = "update ".GALAXIA_TABLE_PREFIX."instances set properties= ? where instanceId= ?";
    $this->query($query,array($props,$iid));
  }
  
  function set_instance_name($iid,$name)
  {
    $query = "update ".GALAXIA_TABLE_PREFIX."instances set name= ? where instanceId= ?";
    $this->query($query,array($name,$iid));
  }
  
  function set_instance_owner($iid,$owner)
  {
    $query = "update ".GALAXIA_TABLE_PREFIX."instances set owner=? where instanceId=?";
    $this->query($query, array($owner, $iid));
  }
  
  function set_instance_status($iid,$status)
  {
    $query = "update ".GALAXIA_TABLE_PREFIX."instances set status=? where instanceId=?";
    $this->query($query, array($status, $iid)); 
  }
  
  function set_instance_destination($iid,$activityId)
  {
    $query = "delete from ".GALAXIA_TABLE_PREFIX."instance_activities where instanceId=?";
    $this->query($query, array($iid));
    $query = "insert into ".GALAXIA_TABLE_PREFIX."instance_activities(instanceId,activityId,user,status) values(?,?,?,?)";
    $this->query($query, array($iid, $activityId, '*', 'running'));
  }
  
  function set_instance_user($iid,$activityId,$user)
  {
    $query = "update ".GALAXIA_TABLE_PREFIX."instance_activities set user=?, status=? where instanceId=? and activityId=?";
    $this->query($query, array($user, 'running', $iid, $activityId));  
  }

}    

?>

<?php
include_once(GALAXIA_LIBRARY.'/src/ProcessManager/BaseManager.php');
//!! RoleManager
//! A class to maniplate roles.
/*!
  This class is used to add,remove,modify and list
  roles used in the Workflow engine.
  Roles are managed in a per-process level, each
  role belongs to some process.
*/

/*!TODO
  Add a method to check if a role name exists in a process (to be used
  to prevent duplicate names)
*/

class RoleManager extends BaseManager {

  function get_role_id($pid,$name)
  {
    return ($this->getOne("select roleId from ".self::tbl('roles')." where name=? and id=?",array($name,$pid)));
  }

  /*!
    Gets a role fields are returned as an asociative array
  */
  function get_role($id, $roleId)
  {
    $query = "select * from ".self::tbl('roles')." where `id`=? and `roleId`=?";
  $result = $this->query($query,array($id, $roleId));
  $res = $result->fetchRow();
  return $res;
  }

  /*!
    Indicates if a role exists
  */
  function role_name_exists($pid,$name)
  {
    return ($this->getOne("select count(*) from ".self::tbl('roles')."where id=? and name=?",array($pid,$name)));
  }

  /*!
    Maps a user to a role
  */
  function map_user_to_role($id,$user,$roleId)
  {
      $query = "delete from ".self::tbl('user_roles')." where `roleId`=? and `user`=?";
      $this->query($query,array($roleId, $user));
      $query = "insert into ".self::tbl('user_roles')."(`id`, `user`, `roleId`) values(?,?,?)";
      $this->query($query,array($id,$user,$roleId));
  }

  /*!
    Removes a mapping
  */
  function remove_mapping($user,$roleId)
  {
      $query = "delete from ".self::tbl('user_roles')." where `user`=? and `roleId`=?";
      $this->query($query,array($user, $roleId));
  }

  /*!
    List mappings
  */
  function list_mappings($id,$offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = $this->convert_sortmode($sort_mode);
    if($find) {
      // no more quoting here - this is done in bind vars already
      $findesc = '%'.$find.'%';
      $query = "select `name`,`gr`.`roleId`,`user` from ".self::tbl('roles')." gr, ".self::tbl('user_roles')." gur where `gr`.`roleId`=`gur`.`roleId` and `gur`.`id`=? and ((`name` like ?) or (`user` like ?) or (`description` like ?)) order by $sort_mode";
      $result = $this->query($query,array($id,$findesc,$findesc,$findesc), $maxRecords, $offset);
      $query_cant = "select count(*) from ".self::tbl('roles')." gr, ".self::tbl('user_roles')." gur where `gr`.`roleId`=`gur`.`roleId` and `gur`.`id`=? and ((`name` like ?) or (`user` like ?) or (`description` like ?))";
      $cant = $this->getOne($query_cant,array($id,$findesc,$findesc,$findesc));
    } else {
      $query = "select `name`,`gr`.`roleId`,`user` from ".self::tbl('roles')."gr, ".self::tbl('user_roles')." gur where `gr`.`roleId`=`gur`.`roleId` and `gur`.`id`=? order by $sort_mode";
      $result = $this->query($query,array($id), $maxRecords, $offset);
      $query_cant = "select count(*) from ".self::tbl('roles')."gr, ".self::tbl('user_roles')." gur where `gr`.`roleId`=`gur`.`roleId` and `gur`.`id`=?";
      $cant = $this->getOne($query_cant,array($id));
    }
    $ret = Array();
    while($res = $result->fetchRow()) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  /*!
    Lists roles at a per-process level
  */
  function list_roles($id,$offset,$maxRecords,$sort_mode,$find,$where='')
  {
    $sort_mode = $this->convert_sortmode($sort_mode);
    if($find) {
      // no more quoting here - this is done in bind vars already
      $findesc = '%'.$find.'%';
      $mid=" where id=? and ((name like ?) or (description like ?))";
      $bindvars = array($id,$findesc,$findesc);
    } else {
      $mid=" where id=? ";
      $bindvars = array($id);
    }
    if($where) {
      $mid.= " and ($where) ";
    }
    $query = "select * from ".self::tbl('roles')." $mid order by $sort_mode";
    $query_cant = "select count(*) from ".self::tbl('roles')." $mid";
    $result = $this->query($query,$bindvars,$maxRecords,$offset);
    $cant = $this->getOne($query_cant,$bindvars);
    $ret = Array();
    while($res = $result->fetchRow()) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }



  /*!
    Removes a role.
  */
  function remove_role($id, $roleId)
  {
    $query = "delete from ".self::tbl('roles')." where `id`=? and `roleId`=?";
    $this->query($query,array($id, $roleId));
    $query = "delete from ".self::tbl('activity_roles')." where `roleId`=?";
    $this->query($query,array($roleId));
    $query = "delete from ".self::tbl('user_roles')." where `roleId`=?";
    $this->query($query,array($roleId));
  }

  /*!
    Updates or inserts a new role in the database, $vars is an asociative
    array containing the fields to update or to insert as needed.
    $id is the processId
    $roleId is the roleId
  */
  function replace_role($id, $roleId, $vars)
  {
    $TABLE_NAME = self::tbl('roles');
    $now = date("U");
    $vars['lastModif']=$now;
    $vars['id']=$id;

    if($roleId) {
      // update mode
      $first = true;
      $query ="update $TABLE_NAME set";
      $bindvars = array();
      foreach($vars as $key=>$value) {
        if(!$first) $query.= ',';
        $query.= " $key=? ";
        $bindvars[] = $value;
        $first = false;
      }
      $query .= " where id=? and roleId=? ";
      $bindvars[] = $pid; $bindvars[] = $roleId;
      $this->query($query,$bindvars);
    } else {
      $name = $vars['name'];
      if ($this->getOne("select count(*) from ".self::tbl('roles')." where id=? and name=?",array($id,$name))) {
        return false;
      }
      unset($vars['roleId']);
      // insert mode
      $first = true;
      $query = "insert into $TABLE_NAME(";
      foreach(array_keys($vars) as $key) {
        if(!$first) $query.= ',';
        $query.= "$key";
        $first = false;
      }
      $query .=") values(";
      $first = true;
      $bindvars = array();
      foreach(array_values($vars) as $value) {
        if(!$first) $query.= ',';
        $query.= "?";
        $bindvars[] = $value;
        $first = false;
      }
      $query .=")";
      $this->query($query,$bindvars);
      $roleId = $this->getOne("select max(roleId) from $TABLE_NAME where id=? and lastModif=?",array($id,$now));
    }
    // Get the id
    return $roleId;
  }
}

?>

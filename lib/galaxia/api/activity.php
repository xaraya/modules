<?php
include_once (GALAXIA_LIBRARY.'/common/base.php');
/**
 * Base class for Workflow activities
 *
 * This class represents activities, and must be derived for
 * each activity type supported in the system. Derived activities extending this
 * class can be found in the activities subfolder.
 * This class is observable.
 *
**/
class WorkflowActivity extends Base
{
    public $name;
    public $description;
    public $isInteractive;
    public $isAutoRouted;

    public $outbound=Array();
    public $inbound=Array();

    public $pId;
    public $activityId;
    public $expirationTime = 0;

    /**
     * Factory method returning an activity of the desired type
     * loading the information from the database.
     *
    **/
    static function &get($activityId)
    {
        // Get an object with a db object for now
        $dummy = new Base();
        $query = "select * from ".self::tbl('activities')." where `activityId`=?";
        $result = $dummy->query($query,array($activityId));

        if(!$result->numRows()) return false;
        $res = $result->fetchRow();
        switch($res['type']) {
          case 'start':
            include_once (GALAXIA_LIBRARY.'/api/activities/start.php');
            $act = new StartActivity();
            break;
          case 'end':
            include_once (GALAXIA_LIBRARY.'/api/activities/end.php');
            $act = new EndActivity();
            break;
          case 'join':
            include_once (GALAXIA_LIBRARY.'/api/activities/join.php');
            $act = new JoinActivity();
            break;
          case 'split':
            include_once (GALAXIA_LIBRARY.'/api/activities/split.php');
            $act = new SplitActivity();
            break;
          case 'standalone':
            include_once (GALAXIA_LIBRARY.'/api/activities/standalone.php');
            $act = new StandaloneActivity();
            break;
          case 'switch':
            include_once (GALAXIA_LIBRARY.'/api/activities/switch.php');
            $act = new SwitchActivity();
            break;
          case 'activity':
            include_once (GALAXIA_LIBRARY.'/api/activities/standard.php');
            $act = new StandardActivity();
            break;
          default:
            trigger_error('Unknown activity type:'.$res['type'],E_USER_WARNING);
        }

        $act->setName($res['name']);
        $act->setProcessId($res['pId']);
        $act->setDescription($res['description']);
        $act->setIsInteractive($res['isInteractive']);
        $act->setIsAutoRouted($res['isAutoRouted']);
        $act->setActivityId($res['activityId']);

        //Now get forward transitions
        //Now get backward transitions
        return $act;
    }

    /* Returns the normalized name for the activity */
    function getNormalizedName()
    {
        return self::normalize($this->getName());
    }

    /* Various getters / setters */
    function setName($name)        { $this->name=$name; }
    function getName()             { return $this->name;}

    function setDescription($desc) { $this->description=$desc;  }
    function getDescription()      { return $this->description; }

    function getType()             { return $this->type;  }

    function setIsInteractive($is) { $this->isInteractive=$is;  }
    function isInteractive()       { return $this->isInteractive == 'y';  }

    function setIsAutoRouted($is)  { $this->isAutoRouted = $is;  }
    function isAutoRouted()        { return $this->isAutoRouted == 'y';  }

    function setProcessId($pid)    { $this->pId=$pid;  }
    function getProcessId()        { return $this->pId;  }

    function setActivityId($id)    { $this->activityId=$id;  }
    function getActivityId()       { return $this->activityId;  }

    /**
     * Return the shape of the activity
     *
     * @todo just a name now, could be an object later
     *
    **/
    public function getShape()  {  return $this->shape; }

    /**
     * Role manipulation for this activity has the following parts:
     * -    addRole($id)
     * - removeRole($id)
     * -   getRoles()
     *
     * @todo work with real Role objects
     * @todo cache getRoles again.
    **/
    function getRoles()
    {
        $query = "select activityId,roles.roleId,roles.name
                from ".self::tbl('activity_roles')."  gar, ".self::tbl('roles')."  roles
                where roles.roleId = gar.roleId and activityId=?";
        $result = $this->query($query,array($this->activityId));
        $ret = Array();
        while($res = $result->fetchRow()) {
            $ret[] = $res;
        }
        return $ret;
    }

    function addRole($roleId)
    {
        $this->removeRole($roleId);
        $query = "insert into ".self::tbl('activity_roles')." (`activityId`,`roleId`) values(?,?)";
        $this->query($query,array($this->activityId, $roleId));
    }
    function removeRole($roleId)
    {
        $query = "delete from ".self::tbl('activity_roles')." where activityId=? and roleId=?";
        $this->query($query,array($this->activityId, $roleId));
    }
    /** END role manipulation **/

    function removeTransitions()
    {
        $query = "delete from ".self::tbl('transitions')."  where pId=? and (actFromId=? or actToId=?)";
        $this->query($query, array($this->pId, $this->activityId, $this->activityId));
    }

    /** METHODS WHICH BELONG SOMEWHERE ELSE */

    /**
     * Returns an Array of roleIds for the given user
     *
     * @todo This is a method which does not belong here, but in a user object of some sort (which we dont have)
     *
    **/
    function getUserRoles($user)
    {
        $query = "select `roleId` from ".self::tbl('user_roles')." where `user`=?";
        $result=$this->query($query,array($user));
        $ret = Array();
        while($res = $result->fetchRow()) {
            $ret[] = $res['roleId'];
        }
        return $ret;
    }

    /**
     *  Returns an Array of asociative arrays with roleId and name for the given user
     *
     * @todo This is a method which does not belong here, but in a user object of some sort (which we dont have)
     *
    **/
    function getActivityRoleNames()
    {
        $aid = $this->activityId;
        $query = "select gr.`roleId`, `name` from ".self::tbl('activity_roles')." gar, ".self::tbl('roles')." gr where gar.`roleId`=gr.`roleId` and gar.`activityId`=?";
        $result=$this->query($query,array($aid));
        $ret = Array();
        while($res = $result->fetchRow()) {
          $ret[] = $res;
        }
        return $ret;
    }

     /*
    /**
     * Checks if a user has a certain role (by name) for this activity,
     * e.g.
     * $isadmin = $activity->checkUserRole($user,'admin');
     *
     * @todo This is a method which does not belong here, but in a user object of some sort (which we dont have)
     *
    **/
     function checkUserRole($user,$rolename)
     {
         $aid = $this->activityId;
         return $this->getOne("
             select count(*)
             from ".self::tbl('activity_roles')." gar, ".self::tbl('user_roles')."gur, ".self::tbl('roles')."gr
             where gar.`roleId`=gr.`roleId` and gur.`roleId`=gr.`roleId` and gar.`activityId`=? and gur.`user`=? and gr.`name`=?",array($aid, $user, $rolename));
     }
}
?>

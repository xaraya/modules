<?php
include_once(GALAXIA_LIBRARY.'/src/ProcessManager/BaseManager.php');
include_once(GALAXIA_LIBRARY.'/src/API/BaseActivity.php');

//!! ActivityManager
//! A class to maniplate process activities and transitions
/*!
  This class is used to add,remove,modify and list
  activities used in the Workflow engine.
  Activities are managed in a per-process level, each
  activity belongs to some process.
*/
class ActivityManager extends BaseManager
{
    private $factory;
    public $error='';

    function __construct()
    {
        parent::__construct();
        // probably rename the class later
        $this->factory = new BaseActivity();
    }

    /**
     * @todo This doesn't belong here
    **/
    function get_error()
    {
        return $this->error;
    }

    /**
     * Activity getter
     *
     */
    public function &getActivity($id)
    {
        $act = $this->factory->getActivity($id);
        return $act;
    }


  /*!
   Gets the roles asociated to an activity
  */
  function get_activity_roles($activityId)
  {
    $query = "select activityId,roles.roleId,roles.name
              from ".self::tbl('activity_roles')."  gar, ".self::tbl('roles')."  roles
              where roles.roleId = gar.roleId and activityId=?";
        $result = $this->query($query,array($activityId));
        $ret = Array();
        while($res = $result->fetchRow()) {
            $ret[] = $res;
        }
        return $ret;
    }



    /*!
     Checks if a transition exists
    */
    function transition_exists($pid,$actFromId,$actToId)
    {
        return($this->getOne("select count(*) from ".self::tbl('transitions')." where pId=? and actFromId=? and actToId=?", array($pid, $actFromId, $actToId)));
    }

    /**
     * Adds a transition between two activities
     *
     * @todo make the checks implicit in the Activity* Classes (like: $act->canHaveInbound(), $act->supportsMultipleTransitions() etc.)
     * @todo move this whole method into the Activity* Classes
    **/
    function add_transition($pId, $actFromId, $actToId)
    {
        // No circular transitions allowed
        if($actFromId == $actToId) return false;
        // Rule: if act is not spl-x or spl-a it can't have more than 1 outbound transition.
        $actFrom = $this->getActivity($actFromId);
        $actTo   = $this->getActivity($actToId);
        if(!$actFrom || !$actTo) return false;
        if(!in_array($actFrom->getType(), array('switch','split'))) {
            if($this->getOne("select count(*) from ".self::tbl('transitions')."  where actFromId=?",array($actFromId))) {
                $this->error = tra('Cannot add transition only split activities can have more than one outbound transition');
                return false;
            }
        }

        // Rule: if act is standalone no transitions allowed
        if($actFrom->getType() == 'standalone' || $actTo->getType() == 'standalone') return false;

        // Rule: No inbound to start
        if($actTo->getType() == 'start') return false;

        // Rule: No outbound from end
        if($actFrom->getType() == 'end') return false;


        $query = "delete from ".self::tbl('transitions')."  where `actFromId`=? and `actToId`=?";
        $this->query($query,array($actFromId, $actToId));
        $query = "insert into ".self::tbl('transitions')." (`pId`,`actFromId`,`actToId`) values(?,?,?)";
        $this->query($query,array($pId, $actFromId, $actToId));

        return true;
    }

    /*!
     Removes a transition
    */
    function remove_transition($actFromId, $actToId)
    {
        $query = "delete from ".self::tbl('transitions')." where actFromId=? and actToId=?";
        $this->query($query,array($actFromId, $actToId));
        return true;
    }

    /*!
     Removes all the activity transitions
    */
    function remove_activity_transitions($pId, $aid)
    {
        $query = "delete from ".self::tbl('transitions')."  where pId=? and (actFromId=? or actToId=?)";
        $this->query($query, array($pId, $aid, $aid));
    }


    /*!
     Returns all the transitions for a process
    */
    function get_process_transitions($pId,$actId=0)
    {
        if(!$actId) {
            $query = "select a1.name as actFromName, a2.name as actToName, actFromId, actToId from ".self::tbl('transitions')."gt,".self::tbl('activities')."a1, ".self::tbl('activities')." a2 where gt.actFromId = a1.activityId and gt.actToId = a2.activityId and gt.pId = ?";
            $bindvars = array($pId);
        } else {
            $query = "select a1.name as actFromName, a2.name as actToName, actFromId, actToId from ".self::tbl('transitions')." gt,".self::tbl('activities')." a1, ".self::tbl('activities')." a2 where gt.actFromId = a1.activityId and gt.actToId = a2.activityId and gt.pId = ? and (actFromId = ?)";
            $bindvars = array($pId,$actId);
        }
        $result = $this->query($query, $bindvars);
        $ret = Array();
        while($res = $result->fetchRow()) {
            $ret[] = $res;
        }
        return $ret;
    }

    /**
     * Returns all the activities for a process as
     * an array of Activity Objects.
     *
     * @todo act on a Process object, not on the pId
    */
    function &get_process_activities($pId)
    {
        $query = "select activityId from ".self::tbl('activities')."where pId=?";
        $result = $this->query($query, array($pId));
        $ret = Array();
        while($res = $result->fetchRow()) {
            $ret[] = $this->getActivity($res['activityId']);
        }
        return $ret;
    }

    /*!
     Builds the graph
    */
    //\todo build the real graph
    function build_process_graph($pId)
    {
        $attributes = Array();
        $graph = new Process_GraphViz(true,$attributes);
        $pm = new ProcessManager($this->db);
        $name = $pm->_get_normalized_name($pId);
        $graph->set_pid($name);

        // Nodes are process activities so get
        // the activities and add nodes as needed
        $nodes = $this->get_process_activities($pId);
        foreach($nodes as $node)
        {
            $auto[$node->getName()] = $node->isAutoRouted();
            $graph->addNode(
                $node->getName(),
                array(
                    'URL'=>"foourl?activityId=".$node->getActivityId(),
                    'label'=>$node->getName(),
                    'shape' => $node->getShape(),
                    'color' => $node->isInteractive() ? 'blue' : 'black',
                    'fontsize' =>8,
                    'fontname' => 'Windsor'
                )
            );
        }

        // Now add edges, edges are transitions,
        // get the transitions and add the edges
        $edges = $this->get_process_transitions($pId);
        foreach($edges as $edge)
            {
                if($auto[$edge['actFromName']] == 'y') {
                    $color = 'red';
                } else {
                    $color = 'black';
                }
                $graph->addEdge(array($edge['actFromName'] => $edge['actToName']), array('color'=>$color));
            }


        // Save the map image and the image graph
        $graph->image_and_map();
        unset($graph);
        return true;
    }


    /*!
     Validates if a process can be activated checking the
     process activities and transitions the rules are:
     0) No circular activities
     1) Must have only one a start and end activity
     2) End must be reachable from start
     3) Interactive activities must have a role assigned
     4) Roles should be mapped
     5) Standalone activities cannot have transitions
     6) Non intractive activities non-auto routed must have some role
     so the user can "send" the activity
    */
    function validate_process_activities($pId)
    {
        $errors = Array();
        // Pre rule no cricular activities
        $cant = $this->getOne("select count(*) from ".self::tbl('transitions')." where pId=? and actFromId=actToId",array($pId));
        if($cant) {
            $errors[] = tra('Circular reference found some activity has a transition leading to itself');
        }

        // Rule 1 must have exactly one start and end activity
        $cant = $this->getOne("select count(*) from ".self::tbl('activities')." where pId=? and type=?",array($pId, 'start'));
        if($cant < 1) {
            $errors[] = tra('Process does not have a start activity');
        }
        $cant = $this->getOne("select count(*) from ".self::tbl('activities')."where pId=? and type=?",array($pId, 'end'));
        if($cant != 1) {
            $errors[] = tra('Process does not have exactly one end activity');
        }

        // Rule 2 end must be reachable from start
        $nodes = Array();
        $endId = $this->getOne("select activityId from ".self::tbl('activities')."where pId=? and type=?",array($pId, 'end'));
        $aux['id']=$endId;
        $aux['visited']=false;
        $nodes[] = $aux;

        $startId = $this->getOne("select activityId from ".self::tbl('activities')."where pId=? and type=?",array($pId,'start'));
        $start_node['id']=$startId;
        $start_node['visited']=true;

        while($this->_list_has_unvisited_nodes($nodes) && !$this->_node_in_list($start_node,$nodes)) {
            for($i=0;$i<count($nodes);$i++) {
                $node=&$nodes[$i];
                if(!$node['visited']) {
                    $node['visited']=true;
                    $query = "select actFromId from ".self::tbl('transitions')."where actToId=?";
                    $result = $this->query($query, array($node['id']));
                    $ret = Array();
                    while($res = $result->fetchRow()) {
                        $aux['id'] = $res['actFromId'];
                        $aux['visited']=false;
                        if(!$this->_node_in_list($aux,$nodes)) {
                            $nodes[] = $aux;
                        }
                    }
                }
            }
        }

        if(!$this->_node_in_list($start_node,$nodes)) {
            // Start node is NOT reachable from the end node
            $errors[] = tra('End activity is not reachable from start activity');
        }

        //Rule 3: interactive activities must have a role
        //assigned.
        //Rule 5: standalone activities can't have transitions
        $query = "select * from ".self::tbl('activities')." where pId = ?";
        $result = $this->query($query, array($pId));
        while($res = $result->fetchRow()) {
            $aid = $res['activityId'];
            if($res['isInteractive'] == 'y') {
                $cant = $this->getOne("select count(*) from ".self::tbl('activity_roles')." where activityId=?",array($res['activityId']));
                if(!$cant) {
                    $errors[] = tra('Activity').': '.$res['name'].tra(' is interactive but has no role assigned');
                }
            } else {
                if( $res['type'] != 'end' && $res['isAutoRouted'] == 'n') {
                    $cant = $this->getOne("select count(*) from".self::tbl('activity_roles')." where activityId=?",array($res['activityId']));
                    if(!$cant) {
                        $errors[] = tra('Activity').': '.$res['name'].tra(' is non-interactive and non-autorouted but has no role assigned');
                    }
                }
            }
            if($res['type']=='standalone') {
                if($this->getOne("select count(*) from ".self::tbl('transitions')."where actFromId=? or actToId=?",array($aid,$aid))) {
                    $errors[] = tra('Activity').': '.$res['name'].tra(' is standalone but has transitions');
                }
            }

        }


        //Rule4: roles should be mapped
        $query = "select * from ".self::tbl('roles')." where pId = ?";
        $result = $this->query($query, array($pId));
        while($res = $result->fetchRow()) {
            $cant = $this->getOne("select count(*) from ".self::tbl('user_roles')." where roleId=?",array($res['roleId']));
            if(!$cant) {
                $errors[] = tra('Role').': '.$res['name'].tra(' is not mapped');
            }
        }


        // End of rules

        // Validate process sources
        $serrors=$this->validate_process_sources($pId);
        $errors = array_merge($errors,$serrors);

        $this->error = $errors;



        $isValid = (count($errors)==0) ? 'y' : 'n';

        $query = "update ".self::tbl('processes')." set isValid=? where pId=?";
        $this->query($query, array($isValid,$pId));

        $this->_label_nodes($pId);

        return ($isValid=='y');


    }

    /*!
     Validate process sources
     Rules:
     1) Interactive activities (non-standalone) must use complete()
     2) Standalone activities must not use $instance
     3) Switch activities must use setNextActivity
     4) Non-interactive activities cannot use complete()
    */
    function validate_process_sources($pid)
    {
        $errors=Array();
        $procname= $this->getOne("select normalized_name from ".self::tbl('processes')." where pId=?",array($pid));

        $query = "select * from ".self::tbl('activities')."where pId=?";
        $result = $this->query($query, array($pid));
        while($res = $result->fetchRow()) {
            $actname = $res['normalized_name'];
            $source = GALAXIA_PROCESSES."/$procname/code/activities/$actname".'.php';
            if (!file_exists($source)) {
                continue;
            }
            $fp = fopen($source,'r');
            $data='';
            while(!feof($fp)) {
                $data.=fread($fp,8192);
            }
            fclose($fp);
            if($res['type']=='standalone') {
                if(strstr($data,'$instance')) {
                    $errors[] = tra('Activity '.$res['name'].' is standalone and is using the $instance object');
                }
            } else {
                if($res['isInteractive']=='y') {
                    if(!strstr($data,'$instance->complete()')) {
                        $errors[] = tra('Activity '.$res['name'].' is interactive so it must use the $instance->complete() method');
                    }
                } else {
                    if(strstr($data,'$instance->complete()')) {
                        $errors[] = tra('Activity '.$res['name'].' is non-interactive so it must not use the $instance->complete() method');
                    }
                }
                if($res['type']=='switch') {
                    if(!strstr($data,'$instance->setNextActivity(')) {
                        $errors[] = tra('Activity '.$res['name'].' is switch so it must use $instance->setNextActivity($actname) method');
                    }
                }
            }
        }
        return $errors;
    }

    /*!
     Indicates if an activity with the same name exists
    */
    function activity_name_exists($pId,$name)
    {
        $name = $this->_normalize_name($name);
        return $this->getOne("select count(*) from ".self::tbl('activities')." where pId=? and normalized_name=?",array($pId,$name));
    }
    /*!
     Lists activities at a per-process level
    */
    function list_activities($pId,$offset,$maxRecords,$sort_mode,$find,$where='')
    {
        $sort_mode = str_replace("_"," ",$sort_mode);
        if($find) {
            $findesc = '%'.$find.'%';
            $mid=" where pId=? and ((name like ?) or (description like ?))";
            $bindvars = array($pId,$findesc,$findesc);
        } else {
            $mid=" where pId=? ";
            $bindvars = array($pId);
        }
        if($where) {
            $mid.= " and ($where) ";
        }
        $query = "select * from ".self::tbl('activities')." $mid order by $sort_mode";
        $query_cant = "select count(*) from ".self::tbl('activities')." $mid";
        $result = $this->query($query,$bindvars,$maxRecords,$offset);
        $cant = $this->getOne($query_cant,$bindvars);
        $ret = Array();
        while($res = $result->fetchRow()) {
            $res['roles'] = $this->getOne("select count(*) from ".self::tbl('activity_roles')." where activityId=?",array($res['activityId']));
            $ret[] = $res;
        }
        $retval = Array();
        $retval["data"] = $ret;
        $retval["cant"] = $cant;
        return $retval;
    }



    /*!
     Removes a activity.
    */
    function remove_activity($pId, $activityId)
    {
        $pm = new ProcessManager($this->db);
        $proc_info = $pm->get_process($pId);
        $actname = $this->_get_normalized_name($activityId);
        $query = "delete from ".self::tbl('activities')." where pId=? and activityId=?";
        $this->query($query, array($pId, $activityId));
        $query = "select actFromId,actToId from ".self::tbl('transitions')." where actFromId=? or actToId=?";
        $result = $this->query($query,array($activityId, $activityId));
        while($res = $result->fetchRow()) {
            $this->remove_transition($res['actFromId'], $res['actToId']);
        }
        $query = "delete from ".self::tbl('activity_roles')." where activityId=?";
        $this->query($query, array($activityId));
        // And we have to remove the user and compiled files
        // for this activity
        $procname = $proc_info['normalized_name'];
        if (file_exists(GALAXIA_PROCESSES."/$procname/code/activities/$actname".'.php')) {
            unlink(GALAXIA_PROCESSES."/$procname/code/activities/$actname".'.php');
        }
        if (file_exists(GALAXIA_PROCESSES."/$procname/code/templates/$actname".'.tpl')) {
            unlink(GALAXIA_PROCESSES."/$procname/code/templates/$actname".'.tpl');
        }
        if (file_exists(GALAXIA_PROCESSES."/$procname/compiled/$actname".'.php')) {
            unlink(GALAXIA_PROCESSES."/$procname/compiled/$actname".'.php');
        }
        return true;
    }

    /*!
     Updates or inserts a new activity in the database, $vars is an asociative
     array containing the fields to update or to insert as needed.
     $pId is the processId
     $activityId is the activityId
    */
    function replace_activity($pId, $activityId, $vars)
    {
        $TABLE_NAME = self::tbl('activities');
        $now = date("U");
        $vars['lastModif']=$now;
        $vars['pId']=$pId;
        $vars['normalized_name'] = $this->_normalize_name($vars['name']);

        $pm = new ProcessManager($this->db);
        $proc_info = $pm->get_process($pId);

        if($activityId) {
            $oldname = $this->_get_normalized_name($activityId);
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
            $query .= " where pId=? and activityId=? ";
            $bindvars[] = $pId; $bindvars[] = $activityId;
            $this->query($query, $bindvars);

            $newname = $vars['normalized_name'];
            // if the activity is changing name then we
            // should rename the user_file for the activity
            // remove the old compiled file and recompile
            // the activity

            $user_file_old = GALAXIA_PROCESSES.'/'.$proc_info['normalized_name'].'/code/activities/'.$oldname.'.php';
            $user_file_new = GALAXIA_PROCESSES.'/'.$proc_info['normalized_name'].'/code/activities/'.$newname.'.php';
            rename($user_file_old, $user_file_new);

            $user_file_old = GALAXIA_PROCESSES.'/'.$proc_info['normalized_name'].'/code/templates/'.$oldname.'.tpl';
            $user_file_new = GALAXIA_PROCESSES.'/'.$proc_info['normalized_name'].'/code/templates/'.$newname.'.tpl';
            if ($user_file_old != $user_file_new) {
                @rename($user_file_old, $user_file_new);
            }


            $compiled_file = GALAXIA_PROCESSES.'/'.$proc_info['normalized_name'].'/compiled/'.$oldname.'.php';
            unlink($compiled_file);
            $this->compile_activity($pId,$activityId);


        } else {

            // When inserting activity names can't be duplicated
            if($this->activity_name_exists($pId, $vars['name'])) {
                return false;
            }
            unset($vars['activityId']);
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
            foreach(array_values($vars) as $value) {
                if(!$first) $query.= ',';
                if(!is_numeric($value)) $value="'".$value."'";
                $query.= "$value";
                $first = false;
            }
            $query .=")";
            $this->query($query);
            $activityId = $this->getOne("select max(activityId) from $TABLE_NAME where pId=$pId and lastModif=$now");
            $ret = $activityId;
            if(!$activityId) {
                throw new Exception("No result from: select max(activityId) from $TABLE_NAME where pId=$pId and lastModif=$now");
            }
            // Should create the code file
            $procname = $proc_info["normalized_name"];
            $fw = fopen(GALAXIA_PROCESSES."/$procname/code/activities/".$vars['normalized_name'].'.php','w');
            fwrite($fw,'<'.'?'.'php'."\n".'?'.'>');
            fclose($fw);

            if($vars['isInteractive']=='y') {
                $fw = fopen(GALAXIA_PROCESSES."/$procname/code/templates/".$vars['normalized_name'].'.tpl','w');
                if (defined('GALAXIA_TEMPLATE_HEADER') && GALAXIA_TEMPLATE_HEADER) {
                    fwrite($fw,GALAXIA_TEMPLATE_HEADER . "\n");
                }
                fclose($fw);
            }

            $this->compile_activity($pId,$activityId);

        }
        // Get the id
        return $activityId;
    }

    /*!
     Sets if an activity is interactive or not
    */
    function set_interactivity($pId, $actid, $value)
    {
        $query = "update ".self::tbl('activities')."set isInteractive=? where pId=? and activityId=?";
        $this->query($query, array($value, $pId, $actid));
        // If template does not exist then create template
        $this->compile_activity($pId,$actid);
    }

    /*!
     Sets if an activity is auto routed or not
    */
    function set_autorouting($pId, $actid, $value)
    {
        $query = "update ".self::tbl('activities')." set isAutoRouted=? where pId=? and activityId=?";
        $this->query($query, array($value, $pId, $actid));
    }


    /*!
     Compiles activity
    */
    function compile_activity($pId, $activityId)
    {
        $act = $this->getActivity($activityId);
        $actname = $act->getNormalizedName();
        $acttype = $act->getType();
        $pm = new ProcessManager($this->db);
        $proc_info = $pm->get_process($pId);
        $compiled_file = GALAXIA_PROCESSES.'/'.$proc_info['normalized_name'].'/compiled/'.$actname.'.php';
        $template_file = GALAXIA_PROCESSES.'/'.$proc_info['normalized_name'].'/code/templates/'.$actname.'.tpl';
        $user_file = GALAXIA_PROCESSES.'/'.$proc_info['normalized_name'].'/code/activities/'.$actname.'.php';
        $pre_file = GALAXIA_LIBRARY.'/compiler/'.$acttype.'_pre.php';
        $pos_file = GALAXIA_LIBRARY.'/compiler/'.$acttype.'_pos.php';
        $fw = fopen($compiled_file,"wb");

        // First of all add an include to to the shared code
        $shared_file = GALAXIA_PROCESSES.'/'.$proc_info['normalized_name'].'/code/shared.php';

        fwrite($fw, '<'."?php include_once('$shared_file'); ?".'>'."\n");

        // Before pre shared
        $fp = fopen(GALAXIA_LIBRARY.'/compiler/_shared_pre.php',"rb");
        while (!feof($fp)) {
            $data = fread($fp, 4096);
            fwrite($fw,$data);
        }
        fclose($fp);

        // Now get pre and pos files for the activity
        $fp = fopen($pre_file,"rb");
        while (!feof($fp)) {
            $data = fread($fp, 4096);
            fwrite($fw,$data);
        }
        fclose($fp);

        // Get the user data for the activity
        $fp = fopen($user_file,"rb");
        while (!feof($fp)) {
            $data = fread($fp, 4096);
            fwrite($fw,$data);
        }
        fclose($fp);

        // Get pos and write
        $fp = fopen($pos_file,"rb");
        while (!feof($fp)) {
            $data = fread($fp, 4096);
            fwrite($fw,$data);
        }
        fclose($fp);

        // Shared pos
        $fp = fopen(GALAXIA_LIBRARY.'/compiler/_shared_pos.php',"rb");
        while (!feof($fp)) {
            $data = fread($fp, 4096);
            fwrite($fw,$data);
        }
        fclose($fp);

        fclose($fw);

        //Copy the templates

        if($act->isInteractive() && !file_exists($template_file)) {
            $fw = fopen($template_file,'w');
            if (defined('GALAXIA_TEMPLATE_HEADER') && GALAXIA_TEMPLATE_HEADER) {
                fwrite($fw,GALAXIA_TEMPLATE_HEADER . "\n");
            }
            fclose($fw);
        }
        if($act->isInteractive() && file_exists($template_file)) {
            @unlink($template_file);
            if (GALAXIA_TEMPLATES && file_exists(GALAXIA_TEMPLATES.'/'.$proc_info['normalized_name']."/$actname.tpl")) {
                @unlink(GALAXIA_TEMPLATES.'/'.$proc_info['normalized_name']."/$actname.tpl");
            }
        }
        if (GALAXIA_TEMPLATES && file_exists($template_file)) {
            @copy($template_file,GALAXIA_TEMPLATES.'/'.$proc_info['normalized_name']."/$actname.tpl");
        }
    }

    /**
     * Returns activity id by pid,name (activity names are unique)
     *
     * @todo called by ProcessManager.php line 258, so can NOT be private yet.
    **/
    function _get_activity_id_by_name($pid,$name)
    {
        if($this->getOne("select count(*) from ".self::tbl('activities')."where pId=? and name=?",array($pid, $name))) {
            return($this->getOne("select activityId from ".self::tbl('activities')." where pId=? and name=?",array($pid,$name)));
        } else {
            return '';
        }
    }



    /*!
     \private Returns true if a list contains unvisited nodes
     list members are asoc arrays containing id and visited
    */
    private function _list_has_unvisited_nodes($list)
    {
        foreach($list as $node) {
            if(!$node['visited']) return true;
        }
        return false;
    }

    /*!
     \private Returns true if a node is in a list
     list members are asoc arrays containing id and visited
    */
    private function _node_in_list($node,$list)
    {
        foreach($list as $a_node) {
            if($node['id'] == $a_node['id']) return true;
        }
        return false;
    }

    /**
     * Normalizes an activity name
     *
     * @todo called by ProcessManager.php line 247, so can NOT be private yet.
    **/
     function _normalize_name($name)
    {
        $name = str_replace(" ","_",$name);
        $name = preg_replace("/[^A-Za-z_]/",'',$name);
        return $name;
    }

    /*!
     \private
     Returns normalized name of an activity
    */
    private function _get_normalized_name($activityId)
    {
        return $this->getOne("select normalized_name from ".self::tbl('activities')." where activityId=?",array($activityId));
    }

    /*!
     \private
     Labels nodes
    */
    private function _label_nodes($pId)
    {


        ///an empty list of nodes starts the process
        $nodes = Array();
        // the end activity id
        $endId = $this->getOne("select activityId from ".self::tbl('activities')."where pId=? and type=?",array($pId,'end'));
        // and the number of total nodes (=activities)
        $cant = $this->getOne("select count(*) from ".self::tbl('activities')."where pId=?",array($pId));
        $nodes[] = $endId;
        $label = $cant;
        $num = $cant;

        $query = "update ".self::tbl('activities')." set flowNum=? where pId=?";
        $this->query($query,array($cant+1,$pId));

        $seen = array();
        while(count($nodes)) {
            $newnodes = Array();
            foreach($nodes as $node) {
                // avoid endless loops
                if (isset($seen[$node])) continue;
                $seen[$node] = 1;
                $query = "update ".self::tbl('activities')." set flowNum=? where activityId=?";
                $this->query($query, array($num, $node));
                $query = "select actFromId from ".self::tbl('transitions')." where actToId=?";
                $result = $this->query($query, array($node));
                $ret = Array();
                while($res = $result->fetchRow()) {
                    $newnodes[] = $res['actFromId'];
                }
            }
            $num--;
            $nodes=Array();
            $nodes=$newnodes;

        }

        $min = $this->getOne("select min(flowNum) from ".self::tbl('activities')."where pId=?",array($pId));
        if(isset($min)) {
            $query = "update ".self::tbl('activities')." set flowNum=flowNum-$min where pId=?";
            $this->query($query, array($pId));
        }
        //$query = "update ".self::tbl('activities')." set flowNum=0 where flowNum=$cant+1";
        //$this->query($query);
    }

}


?>

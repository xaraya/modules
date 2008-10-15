<?php
class CelkoPositionProperty extends DataProperty
{
    public $id           = 30174;
    public $name         = 'celkomsgposition';
    public $desc         = 'Celko Message Position';
    public $reqmodules   = array('messages');

    public $refmsgid;
    public $moving;
    public $position;
    public $rightorleft;
    public $inorout;
    public $parent;
    public $msgexists;
    
    function __construct(ObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);
        $this->template = 'celkomsgposition';
        $this->tplmodule = 'messages';
        $this->filepath   = 'modules/messages/xarproperties';
    }

    public function checkInput($name = '', $value = null)
    {
        if (!xarVarFetch($name . '_refmsgid', 'int:0', $refmsgid)) return;
        if (!xarVarFetch($name . '_position', 'enum:1:2:3:4', $position)) return;
            switch (intval($position)) {
                case 1: // above - same level
                default:
                    $this->rightorleft = 'left';
                    $this->inorout = 'out';
                    break;
                case 2: // below - same level
                    $this->rightorleft = 'right';
                    $this->inorout = 'out';
                    break;
                case 3: // below - child message
                    $this->rightorleft = 'right';
                    $this->inorout = 'in';
                    break;
                case 4: // above - child message
                    $this->rightorleft = 'left';
                    $this->inorout = 'in';
                    break;
            }
        $this->refmsgid = $refmsgid;
        return true;
    }

    public function createValue($itemid=0)
    {
        $n = xarModAPIFunc('messages', 'user', 'get_count');
        if ($n == 1) {
            $itemid = $this->updateposition($itemid);
        } else {

           // Obtain current information on the reference message
           $msg = xarModAPIFunc('messages', 'user', 'get_one', array('id' => $this->refmsgid));

           if ($msg == false) {
               xarSession::setVar('errormsg', xarML('That message does not exist'));
               return false;
           }

           $this->right = $msg['right_id'];
           $this->left = $msg['left_id'];

           /* Find out where you should put the new message in */
           if (
               !($point_of_insertion =
                    xarModAPIFunc('messages','admin','find_point_of_insertion',
                       Array('inorout' => $this->inorout,
                               'rightorleft' => $this->rightorleft,
                               'right' => $this->right,
                               'left' => $this->left
                       )
                   )
              )
              )
           {
               return false;
           }

            /* Find the right parent for this message */
            if (strtolower($this->inorout) == 'in') {
                $parent = (int)$this->refmsgid;
            } else {
                $parent = (int)$msg['pid'];
            }
            $itemid = $this->updateposition($itemid,$parent,$point_of_insertion);
        }
        return true;
    }

    public function updateValue($itemid=0)
    {
        // Obtain current information on the message
        $msg = xarModAPIFunc('messages', 'user', 'get', array('id' => $itemid));

        if ($msg == false) {
           xarSession::setVar('errormsg', xarML('That message does not exist'));
           return false;
        }

        // Get datbase setup
        $dbconn = xarDB::getConn();
        $xartable = xarDB::getTables();
        $messagestable = $xartable['messages'];

       // Obtain current information on the reference message
       $refmsg = xarModAPIFunc('messages', 'user', 'get', array('id'=>$this->refmsgid));

       if ($refmsg == false) {
           xarSession::setVar('errormsg', xarML('That message does not exist'));
           return false;
       }

       // Checking if the reference ID is of a child or itself
       if (
           ($refmsg['left_id'] >= $msg['left_id'])  &&
           ($refmsg['left_id'] <= $msg['right_id'])
          )
       {
            $msg = xarML('Message references siblings.');
            throw new BadParameterException(null, $msg);
       }

       // Find the needed variables for moving things...
       $point_of_insertion =
                   xarModAPIFunc('messages','admin','find_point_of_insertion',
                       Array('inorout' => $this->inorout,
                               'rightorleft' => $this->rightorleft,
                               'right' => $refmsg['right_id'],
                               'left' => $refmsg['left_id']
                       )
                   );
       $size = $msg['right_id'] - $msg['left_id'] + 1;
       $distance = $point_of_insertion - $msg['left_id'];

       // If necessary to move then evaluate
       if ($distance != 0) { // It's Moving, baby!  Do the Evolution!
          if ($distance > 0)
          { // moving forward
              $distance = $point_of_insertion - $msg['right_id'] - 1;
              $deslocation_outside = -$size;
              $between_string = ($msg['right_id'] + 1)." AND ".($point_of_insertion - 1);
          }
          else
          { // $distance < 0 (moving backward)
              $deslocation_outside = $size;
              $between_string = $point_of_insertion." AND ".($msg['left_id'] - 1);
          }

          // TODO: besided portability, also check performance here
          $SQLquery = "UPDATE $messagestable SET
                       left_id = CASE
                        WHEN left_id BETWEEN ".$msg['left_id']." AND ".$msg['right_id']."
                           THEN left_id + ($distance)
                        WHEN left_id BETWEEN $between_string
                           THEN left_id + ($deslocation_outside)
                        ELSE left_id
                        END,
                      right_id = CASE
                        WHEN right_id BETWEEN ".$msg['left_id']." AND ".$msg['right_id']."
                           THEN right_id + ($distance)
                        WHEN right_id BETWEEN $between_string
                           THEN right_id + ($deslocation_outside)
                        ELSE right_id
                        END
                     ";
                     // This seems SQL-92 standard... Its a good test to see if
                     // the databases we are supporting are complying with it. This can be
                     // broken down in 3 simple UPDATES which shouldnt be a problem with any database

            $result = $dbconn->Execute($SQLquery);
            if (!$result) return;

          /* Find the right parent for this message */
          if (strtolower($this->inorout) == 'in') {
              $parent_id = $this->refmsgid;
          } else {
              $parent_id = $refmsg['pid'];
          }
          // Update parent id
          $SQLquery = "UPDATE $messagestable
                       SET pid = ?
                       WHERE id = ?";
        $result = $dbconn->Execute($SQLquery,array($parent_id, $itemid));
        if (!$result) return;

       } 
    }

    public function showInput(Array $data = array())
    {
        $data['itemid'] = isset($data['itemid']) ? $data['itemid'] : $this->value;
        if (!empty($data['itemid'])) {        
            $data['message'] = xarModAPIFunc('messages',
                                              'user',
                                              'get',
                                              array('id' => $data['itemid']));
            $messages = xarModAPIFunc('messages',
                                        'user',
                                        'get_tree',
                                        array('id' => $data['itemid']));

            $data['id'] = $data['itemid'];
        } else {
            $data['message'] = Array('left_id'=>0,'right_id'=>0,'name'=>'','description'=>'', 'image' => '');
            $messages = xarModAPIFunc('messages',
                                        'user',
                                        'get_tree');
            $data['id'] = null;
        }
        
        $message_stack = array();

        foreach ($messages as $key => $message) {
            $messages[$key]['slash_separated'] = '';

            while ((count($message_stack) > 0 ) &&
                   ($message_stack[count($message_stack)-1]['indentation'] >= $message['indentation'])
                  ) {
               array_pop($message_stack);
            }

            foreach ($message_stack as $stack_msg) {
                    $messages[$key]['slash_separated'] .= $stack_msg['title'].'&#160;/&#160;';
            }

            array_push($message_stack, $message);
            $messages[$key]['slash_separated'] .= $message['title'];
        }

        $data['messages'] = $messages;

        return parent::showInput($data);

    }

        /*
    public function showOutput(Array $args = array())
    {
        extract($args);

        if (!isset($value)) $value = $this->value;

        // Get the configuration settings for the form
        foreach ($this->configargs as $configarg) {
            if (isset($$configarg)) {
                $this->$configarg = $$configarg;
            }
            $data[$configarg]   = $this->$configarg;
        }

        // Allow overrides for the properties
        if (isset($object)) {
            $info = DataObjectMaster::getObjectInfo(array('name'  => $object));
            $this->initialization_refobject = $info['name'];
        }
        if (isset($allowinput)) $this->initialization_subiteminput = $allowinput;
        if (isset($linkfield)) $this->initialization_linkfield = $linkfield;

        // Get the properties for the form
        foreach ($this->configargs as $configarg) {
            $data[$configarg]   = $this->$configarg;
        }
        if (isset($label)) $data['label'] = $label;

        // If we already have items send them to the template
        if (!empty($this->items)) $data['items'] = $this->items;

        // If not get the objectlist and the appropriate items in it
        if (!isset($data['items'])) $data['items'] = $this->getObjects($value);
        $data['numberofitems'] = count($data['items']);

        return parent::showOutput($data);
    }
        */
    
    function updateposition($itemid=0, $parent=0, $point_of_insertion=1) 
    {
        
        $dbconn = xarDB::getConn();
        $xartable = xarDB::getTables();
        $messagestable = $xartable['messages'];
        $bindvars = array();
        $bindvars[1] = array();
        $bindvars[2] = array();
        $bindvars[3] = array();

        /* Opening space for the new node */
        $SQLquery[1] = "UPDATE $messagestable
                        SET right_id = right_id + 2
                        WHERE right_id >= ?";
        $bindvars[1][] = $point_of_insertion;

        $SQLquery[2] = "UPDATE $messagestable
                        SET left_id = left_id + 2
                        WHERE left_id >= ?";
        $bindvars[2][] = $point_of_insertion;
        // Both can be transformed into just one SQL-statement, but i dont know if every database is SQL-92 compliant(?)

        $SQLquery[3] = "UPDATE $messagestable SET
                                    pid = ?,
                                    left_id = ?,
                                    right_id = ?
                                     WHERE id = ?";
        $bindvars[3] = array($parent, $point_of_insertion, $point_of_insertion + 1,$itemid);

        for ($i=1;$i<4;$i++) if (!$dbconn->Execute($SQLquery[$i],$bindvars[$i])) return;
    }

}
?>

<?php
class CelkoPositionProperty extends DataProperty
{
    public $id           = 30074;
    public $name         = 'celkoposition';
    public $desc         = 'Celko Position';
    public $reqmodules   = array('categories');

    public $refcid;
    public $moving;
    public $position;
    public $rightorleft;
    public $inorout;
    public $parent;
    public $catexists;
    
    function __construct(ObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);
        $this->template = 'celkoposition';
        $this->tplmodule = 'categories';
        $this->filepath   = 'modules/categories/xarproperties';
    }

    public function checkInput($name = '', $value = null)
    {
        if (!xarVarFetch($name . '_refcid', 'int:0', $refcid)) return;
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
                case 3: // below - child category
                    $this->rightorleft = 'right';
                    $this->inorout = 'in';
                    break;
                case 4: // above - child category
                    $this->rightorleft = 'left';
                    $this->inorout = 'in';
                    break;
            }
        $this->refcid = $refcid;
        return true;
    }

    public function createValue($itemid=0)
    {
        $n = xarModAPIFunc('categories', 'user', 'countcats');
        if ($n == 1) {
            $itemid = $this->updateposition($itemid);
        } else {

           // Obtain current information on the reference category
           $cat = xarModAPIFunc('categories', 'user', 'getcatinfo', array('cid' => $this->refcid));

           if ($cat == false) {
               xarSession::setVar('errormsg', xarML('That category does not exist'));
               return false;
           }

           $this->right = $cat['right'];
           $this->left = $cat['left'];

           /* Find out where you should put the new category in */
           if (
               !($point_of_insertion =
                    xarModAPIFunc('categories','admin','find_point_of_insertion',
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

            /* Find the right parent for this category */
            if (strtolower($this->inorout) == 'in') {
                $parent = (int)$this->refcid;
            } else {
                $parent = (int)$cat['parent'];
            }
            $itemid = $this->updateposition($itemid,$parent,$point_of_insertion);
        }
        return true;
    }

    public function updateValue($itemid=0)
    {
        // Obtain current information on the category
        $cat = xarModAPIFunc('categories', 'user', 'getcatinfo', array('cid' => $itemid));

        if ($cat == false) {
           xarSession::setVar('errormsg', xarML('That category does not exist'));
           return false;
        }

        // Get datbase setup
        $dbconn = xarDB::getConn();
        $xartable = xarDB::getTables();
        $categoriestable = $xartable['categories'];

       // Obtain current information on the reference category
       $refcat = xarModAPIFunc('categories', 'user', 'getcatinfo', array('cid'=>$this->refcid));

       if ($refcat == false) {
           xarSession::setVar('errormsg', xarML('That category does not exist'));
           return false;
       }

       // Checking if the reference ID is of a child or itself
       if (
           ($refcat['left'] >= $cat['left'])  &&
           ($refcat['left'] <= $cat['right'])
          )
       {
            $msg = xarML('Category references siblings.');
            throw new BadParameterException(null, $msg);
       }

       // Find the needed variables for moving things...
       $point_of_insertion =
                   xarModAPIFunc('categories','admin','find_point_of_insertion',
                       Array('inorout' => $this->inorout,
                               'rightorleft' => $this->rightorleft,
                               'right' => $refcat['right'],
                               'left' => $refcat['left']
                       )
                   );
       $size = $cat['right'] - $cat['left'] + 1;
       $distance = $point_of_insertion - $cat['left'];

       // If necessary to move then evaluate
       if ($distance != 0) { // It´s Moving, baby!  Do the Evolution!
          if ($distance > 0)
          { // moving forward
              $distance = $point_of_insertion - $cat['right'] - 1;
              $deslocation_outside = -$size;
              $between_string = ($cat['right'] + 1)." AND ".($point_of_insertion - 1);
          }
          else
          { // $distance < 0 (moving backward)
              $deslocation_outside = $size;
              $between_string = $point_of_insertion." AND ".($cat['left'] - 1);
          }

          // TODO: besided portability, also check performance here
          $SQLquery = "UPDATE $categoriestable SET
                       left_id = CASE
                        WHEN left_id BETWEEN ".$cat['left']." AND ".$cat['right']."
                           THEN left_id + ($distance)
                        WHEN left_id BETWEEN $between_string
                           THEN left_id + ($deslocation_outside)
                        ELSE left_id
                        END,
                      right_id = CASE
                        WHEN right_id BETWEEN ".$cat['left']." AND ".$cat['right']."
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

          /* Find the right parent for this category */
          if (strtolower($this->inorout) == 'in') {
              $parent_id = $this->refcid;
          } else {
              $parent_id = $refcat['parent'];
          }
          // Update parent id
          $SQLquery = "UPDATE $categoriestable
                       SET parent_id = ?
                       WHERE id = ?";
        $result = $dbconn->Execute($SQLquery,array($parent_id, $itemid));
        if (!$result) return;

       } 
    }

    public function showInput(Array $data = array())
    {
        $data['itemid'] = isset($data['itemid']) ? $data['itemid'] : $this->value;
        if (!empty($data['itemid'])) {        
            $data['category'] = xarModAPIFunc('categories',
                                              'user',
                                              'getcatinfo',
                                              array('cid' => $data['itemid']));
            $categories = xarModAPIFunc('categories',
                                        'user',
                                        'getcat',
                                        array('cid' => false,
                                              'eid' => $data['itemid'],
                                              'getchildren' => true));
            $data['cid'] = $data['itemid'];
        } else {
            $data['category'] = Array('left'=>0,'right'=>0,'name'=>'','description'=>'', 'image' => '');
            $categories = xarModAPIFunc('categories',
                                        'user',
                                        'getcat',
                                        array('cid' => false,
                                              'getchildren' => true));
            $data['cid'] = null;
        }
        
        $category_Stack = array ();

        foreach ($categories as $key => $category) {
            $categories[$key]['slash_separated'] = '';

            while ((count($category_Stack) > 0 ) &&
                   ($category_Stack[count($category_Stack)-1]['indentation'] >= $category['indentation'])
                  ) {
               array_pop($category_Stack);
            }

            foreach ($category_Stack as $stack_cat) {
                    $categories[$key]['slash_separated'] .= $stack_cat['name'].'&#160;/&#160;';
            }

            array_push($category_Stack, $category);
            $categories[$key]['slash_separated'] .= $category['name'];
        }

        $data['categories'] = $categories;

        return parent::showInput($data);

    }

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
    
    function updateposition($itemid=0, $parent=0, $point_of_insertion=1) 
    {
        
        $dbconn = xarDB::getConn();
        $xartable = xarDB::getTables();
        $categoriestable = $xartable['categories'];
        $bindvars = array();
        $bindvars[1] = array();
        $bindvars[2] = array();
        $bindvars[3] = array();

        /* Opening space for the new node */
        $SQLquery[1] = "UPDATE $categoriestable
                        SET right_id = right_id + 2
                        WHERE right_id >= ?";
        $bindvars[1][] = $point_of_insertion;

        $SQLquery[2] = "UPDATE $categoriestable
                        SET left_id = left_id + 2
                        WHERE left_id >= ?";
        $bindvars[2][] = $point_of_insertion;
        // Both can be transformed into just one SQL-statement, but i dont know if every database is SQL-92 compliant(?)

        $SQLquery[3] = "UPDATE $categoriestable SET
                                    parent_id = ?,
                                    left_id = ?,
                                    right_id = ?
                                     WHERE id = ?";
        $bindvars[3] = array($parent, $point_of_insertion, $point_of_insertion + 1,$itemid);

        for ($i=1;$i<4;$i++) if (!$dbconn->Execute($SQLquery[$i],$bindvars[$i])) return;
    }

}
?>

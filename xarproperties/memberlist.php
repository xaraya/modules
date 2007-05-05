<?php
/**
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Members module
 */

/**
 * MemberList Property
 * @author Marc Lutolf (mfl@netspan.ch)
 */

    /*
    * Options available to customer selection
    * ===================================
    * Options take the form:
    *   option-type:option-value;
    * option-types:
    *   group:name[,name] - select only members in certain group(s)
    *   member:name[,name] - select only certain customer(s)
    */

class MemberList extends DataProperty
{
    public $id   = 30056;
    public $name = 'memberlist';
    public $desc = 'MemberList';
    public $reqmodules = array('members');

    public $object = 'members_members';
    protected $grouplist = array();
    protected $memberlist = array();

    protected $localmodule;

    function __construct(ObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);

        $this->tplmodule = 'members';
        $this->filepath   = 'modules/members/xarproperties';
    }

    public function showInput(Array $data = array())
    {
        if (isset($data['localmodule'])) {
            $this->localmodule = $data['localmodule'];
        } else {
            $info = xarRequestGetInfo();
            $this->localmodule = $info[0];
            $data['localmodule'] = $this->localmodule;
        }
        $data['regid'] = xarModGetIDFromName($data['localmodule']);
        $this->parsevalidation();
        if (!isset($data['object'])) $data['object'] = $this->object;
        $data['groups'] = $this->grouplist;
        $data['members'] = $this->memberlist;
        $data = array_merge($data, xarModAPIFunc('members','user','view',$data));
        return parent::showInput($data);
    }

    

public function parseValidation($validation = '')
    {
        foreach(preg_split('/(?<!\\\);/', $this->validation) as $option) {
            // Semi-colons can be escaped with a '\' prefix.
            $option = str_replace('\;', ';', $option);
            // An option comes in two parts: option-type:option-value
            if (strchr($option, ':')) {
                list($option_type, $option_value) = explode(':', $option, 2);
                if ($option_type == 'groups') {
                    $this->grouplist = array_merge($this->grouplist, explode(',', $option_value));
                }
                if ($option_type == 'members') {
                    $this->memberlist = array_merge($this->memberlist, explode(',', $option_value));
                }
            }
        }
    }
}
?>

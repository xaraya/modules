<?php
/**
 * Simple number sum for logic captcha
 *
 * @package Xaraya modules
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com
 *
 * @subpackage Formantibot
 * @copyright (C) 2008 2skies.com
 * @link http://xarigami.com/project/formantibot
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */

class securlogic
{
   
    var $testsum = '';
    var $code_entered;
    var $correct_code;
    var $hash_salt = "fg7hg3yg3fd90oi4i";
    var $code;

    function securlogic() 
    {
        $this->setSettings();
        $this->userCodeKey = 'code.' . md5($this->hash_salt . $this->getcurrentip());

    }

    function setSettings() 
    {
        $settings = unserialize(xarModGetVar('formantibot', 'settings'));

        foreach ($settings as $name => $value) {
            $this->$name = $value;
        }
    }

    function display()
    {
        $this->createTest();
        return $this->testsum;

    }
   /*
    * @based on code by Rob Malon <robmalon.com>
    */
    function createTest()
    {
        $firstnum = rand(5,8);
        $secondnum = rand(1,4);
        $coinflip = rand(1,2) % 2;
        $operators = array();
        if($coinflip == 0) {
            $math = $firstnum + $secondnum;
            $operators = array("+","Added To","Plus");
            $operatorschoice = rand(1,3) % 3;
        } else {
            $math = $firstnum - $secondnum;
            $operators = array("-","Minus");
            $operatorschoice = rand(1,2) % 2;
        }
        $this->testsum =  $firstnum . " " . $operators[$operatorschoice] . " " . $secondnum. " = ";
        $this->code = $math;
        $this->saveData();
    }

   
    function saveData()
    {

        xarModSetVar('formantibot', $this->userCodeKey, md5($this->hash_salt . $this->code));
    }

    function validate($userCode)
    {
        $userInput = md5($this->hash_salt . $userCode);
        $original  = xarModGetVar('formantibot', $this->userCodeKey);

        xarModDelVar('formantibot', $this->userCodeKey);

        if($userInput == $original) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    
    function getcurrentip()
    {      
        $userip = xarSessionGetIPAddress();
        return $userip;
    }

} //end class

?>
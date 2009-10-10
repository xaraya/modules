<?php

/**
 * Handle hitcount 'item' hook calls for different actions
 */
class HitcountItemHooks extends ItemHookCallHandler
{
    // specify the name of the hook module here
    public $modname = 'hitcount';
    public $scope   = 'item';

    // specify the different 'item' actions this hook handler will support here
    public $actions = array(
        'create'  => array('type' => 'admin', 'area' => 'API'),
        'delete'  => array('type' => 'admin', 'area' => 'API'),
        'display' => array('type' => 'user',  'area' => 'GUI'),
    );

    // specify an optional method mapper e.g. if you have several actions to support, and you want to import them only on demand
    public $mapper  = array(
/* use later for different action handlers ?
        'create' => array('classname'  => 'HitcountCreateHook',
                          'classfunc'  => 'run',
                          'importname' => 'modules.hitcount.class.hookactions.create'),
*/
    );

    // CHECKME: for 'item' hook calls, we deal with a real dataobject or with the dummy object (= based on extraInfo)

/* add your own action methods here in your child class */

    /**
     * Run the 'create' item API action - move to separate action handler later
     *
     * @param subject mixed the dataobject for object calls, or the dummy object (= based on extraInfo) for module calls
     */
    public function create($subject)
    {
        // list of fields that shouldn't be updated by API actions
        $fixedlist = array('module','itemtype','itemid','returnurl','transform');

    // TODO: validate this way of working in tricky situations
        // do some processing with $subject->hookinput or other properties in this API method
        $hookoutput = xarMod::apiFunc('hitcount','admin','create',
                                      array('objectid'  => $subject->itemid,
                                            'extrainfo' => $subject->hookinput));

        // update the current $subject->hookinput values in the API method if needed
        if (!empty($hookoutput) && is_array($hookoutput)) {
            foreach (array_keys($hookoutput) as $name) {
                if (in_array($name, $fixedlist)) continue;
                $subject->hookinput[$name] = $hookoutput[$name];
            }
        }

        // put the updated values in $subject->hookoutput
        $subject->hookoutput = $subject->hookinput;
        // no need to return anything here
    }

    /**
     * Run the 'delete' item API action - move to separate action handler later
     *
     * @param subject mixed the dataobject for object calls, or the dummy object (= based on extraInfo) for module calls
     */
    public function delete($subject)
    {
        // list of fields that shouldn't be updated by API actions
        $fixedlist = array('module','itemtype','itemid','returnurl','transform');

    // TODO: validate this way of working in tricky situations
        // do some processing with $subject->hookinput or other properties in this API method
        $hookoutput = xarMod::apiFunc('hitcount','admin','delete',
                                      array('objectid'  => $subject->itemid,
                                            'extrainfo' => $subject->hookinput));

        // update the current $subject->hookinput values in the API method if needed
        if (!empty($hookoutput) && is_array($hookoutput)) {
            foreach (array_keys($hookoutput) as $name) {
                if (in_array($name, $fixedlist)) continue;
                $subject->hookinput[$name] = $hookoutput[$name];
            }
        }

        // put the updated values in $subject->hookoutput
        $subject->hookoutput = $subject->hookinput;
        // no need to return anything here
    }

    /**
     * Run the 'display' item GUI action - move to separate action handler later
     *
     * @param subject mixed the dataobject for object calls, or the dummy object (= based on extraInfo) for module calls
     */
    public function display($subject)
    {
    // TODO: validate this way of working in tricky situations
        // generate some GUI output with $subject->hookinput or other properties in this method
        $hookoutput = xarMod::guiFunc('hitcount','user','display',
                                      array('objectid'  => $subject->itemid,
                                            'extrainfo' => $subject->hookinput));

        // add the output of the GUI method to the $subject->hookoutput array, using the hook modname as key
        if (isset($hookoutput)) {
            $subject->hookoutput[$this->modname] = $hookoutput;
        }
        // no need to return anything here
    }
}

?>

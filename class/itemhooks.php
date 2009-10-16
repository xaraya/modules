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
        'view'    => array('type' => 'user',  'area' => 'GUI'),
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
        // do some processing with $subject->hookvalues or other properties in this API method
        $hookvalues = xarMod::apiFunc('hitcount','admin','create',
                                      array('objectid'  => $subject->itemid,
                                            'extrainfo' => $subject->hookvalues));

        // update the current $subject->hookvalues in the API method if needed
        if (!empty($hookvalues) && is_array($hookvalues)) {
            foreach (array_keys($hookvalues) as $name) {
                if (in_array($name, $fixedlist)) continue;
                $subject->hookvalues[$name] = $hookvalues[$name];
            }
        }
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
        // do some processing with $subject->hookvalues or other properties in this API method
        $hookvalues = xarMod::apiFunc('hitcount','admin','delete',
                                      array('objectid'  => $subject->itemid,
                                            'extrainfo' => $subject->hookvalues));

        // update the current $subject->hookvalues in the API method if needed
        if (!empty($hookvalues) && is_array($hookvalues)) {
            foreach (array_keys($hookvalues) as $name) {
                if (in_array($name, $fixedlist)) continue;
                $subject->hookvalues[$name] = $hookvalues[$name];
            }
        }
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
        // generate some GUI output with $subject->hookvalues or other properties in this method
        $hookoutput = xarMod::guiFunc('hitcount','user','display',
                                      array('objectid'  => $subject->itemid,
                                            'extrainfo' => $subject->hookvalues));
        if (!isset($hookoutput)) {
            return;
        }

        // add the output of the GUI method to the $subject->hookoutput array, using the hook modname as key
        $subject->hookoutput[$this->modname] = $hookoutput;
        // no need to return anything here

// CHECKME: and/or add property to $subject with hitcount output for this item ?
        $name = 'hitcount_hook';
        $propinfo = array('name' => $name,
                          'label' => 'Hitcount',
                          'type' => 'textbox', // we'll show the GUI output here
                          //'id' => null,
                          'defaultvalue' => '',
                          'source' => 'dummy',
                          'status' => 33,
                          //'seq' => null,
                          'configuration' => serialize(array('display_tooltip' => 'Number of times this item was displayed')),
                         );
        $subject->addProperty($propinfo);
        if (!empty($subject->fieldlist)) {
            $subject->fieldlist[] = $name;
        }
        // set the output of the GUI method as value for the $subject property
        $subject->properties[$name]->value = $hookoutput;
    }

    /**
     * Run the 'view' items GUI action - this only gets called by the dd ui handler for now
     *
     * @param subject mixed the dataobject for object calls, or the dummy object (= based on extraInfo) for module calls
     */
    public function view($subjectlist)
    {
        if (strtolower(get_class($subjectlist)) == 'dummyhookedobject') {
            // there's nothing we can do for traditional modules calling hooks with extraInfo
            return;
        }

// CHECKME: add property to $subjectlist with hitcount for all $subjectlist->itemids, and/or adapt $subjectlist->items ?
        $name = 'hitcount_hook';
        $propinfo = array('name' => $name,
                          'label' => 'Hitcount',
                          'type' => 'integerbox', // we'll set the hitcount values here
                          //'id' => null,
                          'defaultvalue' => '',
                          'source' => 'dummy',    // force using the dummy datastore here
// CHECKME: status should be configurable here !?
                          'status' => 33,
                          //'seq' => null,
                          'configuration' => serialize(array('display_tooltip' => 'Number of times this item was displayed')),
                         );
        $subjectlist->addProperty($propinfo);
        if (!empty($subjectlist->fieldlist)) {
            $subjectlist->fieldlist[] = $name;
        }

        // get the hitcount for all the items in the subjectlist
        $hits = xarMod::apiFunc('hitcount','user','getitems',
                                array('modid'    => $subjectlist->moduleid,
                                      'itemtype' => $subjectlist->itemtype,
                                      'itemids'  => $subjectlist->itemids));
        // set the hitcount for all the items in the subjectlist
        foreach ($subjectlist->itemids as $itemid) {
            if (isset($hits[$itemid])) {
                $subjectlist->items[$itemid][$name] = $hits[$itemid];
            } else {
                $subjectlist->items[$itemid][$name] = 0;
            }
        }
        // tell the subjectlist to calculate the sum of the hitcounts, just for fun ;-)
        $subjectlist->fieldsummary[$name] = 'Sum';
    }
}

?>

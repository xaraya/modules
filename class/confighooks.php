<?php

/**
 * Handle hitcount 'module' hook calls for different actions
 */
class HitcountConfigHooks extends ConfigHookCallHandler
{
    // specify the name of the hook module here
    public $modname = 'hitcount';
    public $scope   = 'module';

    // specify the different 'module' actions this hook handler will support here
    public $actions = array(
        'remove'       => array('type' => 'admin', 'area' => 'API'),
    );

    // specify an optional method mapper e.g. if you have several actions to support, and you want to import them only on demand
    public $mapper  = array();

    // CHECKME: for 'module' hook calls, we only deal with the dummy object (= based on extraInfo)

/* add your own action methods here in your child class */

    /**
     * Run the 'remove' module API action - TODO: we *must* take into account the itemtype here !
     *
     * @param subject the dummy object (= based on extraInfo)
     */
    public function remove($subject)
    {
    // TODO: validate this way of working in tricky situations
        // do some processing with $subject->hookinput or other properties in this API method
        $hookoutput = xarMod::apiFunc('hitcount','admin','deleteall',
                                      array('objectid'  => $subject->itemid,
                                            'extrainfo' => $subject->hookinput));

        // update the current $subject->hookinput values in the API method if needed
        // put the updated values in $subject->hookoutput
        // no need to return anything here
    }
}

?>

<?php
/**
 * Workflow Module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Workflow Module
 * @link http://xaraya.com/index.php/release/188.html
 * @author Marc Lutolf <mfl@netspan.ch>
 */

sys::import('modules.workflow.class.object');
sys::import('modules.dynamicdata.class.objects.list');

class Activity extends WorkflowObject
{
    function createItem(Array $args = array())
    {
        if(!empty($args['itemid'])) $this->itemid = $args['itemid'];

        $this->properties['normalized_name']->value = $this->normalize($this->properties['name']->value);

        $process = new Process($this->properties['process_id']->value);
        $procNName = $process->getNormalizedName();
        
        // Activity names musr be unique
        if($process->hasActivity($this->properties['name']->value)) return false;

        // Create a code file
        $newname = $this->properties['normalized_name']->value;
        $fw = fopen(GALAXIA_PROCESSES."/$procNName/code/activities/".$newname.'.php','w');
        fwrite($fw,'<'.'?'.'php'."\n".'?'.'>');
        fclose($fw);

        // Create a template file for interactive activities
        if($this->properties['interactive']->value) {
            $fw = fopen(GALAXIA_PROCESSES."/$procNName/code/templates/".$newname.'.xt','w');
            if (defined('GALAXIA_TEMPLATE_HEADER') && GALAXIA_TEMPLATE_HEADER) {
                fwrite($fw,GALAXIA_TEMPLATE_HEADER . "\n");
            }
            fclose($fw);
        }
        $itemid = parent::createItem($args);

        $newAct = WorkflowActivity::get($itemid);
        $newAct->compile();

        return $itemid;
    }

    function updateItem(Array $args = array())
    {
        if(!empty($args['itemid'])) $this->itemid = $args['itemid'];

        $this->properties['normalized_name']->value = $this->normalize($this->properties['name']->value);
        $this->properties['last_modified']->value = time();
        
        $itemid = parent::updateItem($args);
        
        // Rename the files if required
        $newname = $this->properties['normalized_name']->value;
        if (isset($args['oldname']) && !empty($args['oldname']) && ($newname != $args['oldname'])) {
            $process = new Process($this->properties['process_id']->value);
            $procNName = $process->getNormalizedName();
            
            $user_file_old = GALAXIA_PROCESSES.'/'.$procNName.'/code/activities/'.$args['oldname'].'.php';
            $user_file_new = GALAXIA_PROCESSES.'/'.$procNName.'/code/activities/'.$newname.'.php';
            rename($user_file_old, $user_file_new);

            $user_file_old = GALAXIA_PROCESSES.'/'.$procNName.'/code/templates/'.$args['oldname'].'.xt';
            $user_file_new = GALAXIA_PROCESSES.'/'.$procNName.'/code/templates/'.$newname.'.xt';
            if ($user_file_old != $user_file_new) {
                rename($user_file_old, $user_file_new);
            }

            $compiled_file = GALAXIA_PROCESSES.'/'.$procNName.'/compiled/'.$args['oldname'].'.php';
            if (file_exists($compiled_file)) unlink($compiled_file);
            $newAct = WorkflowActivity::get($itemid);
            $newAct->compile();
        }

        return $itemid;
    }

    function deleteItem(Array $args = array())
    {
        if(!empty($args['itemid'])) $this->itemid = $args['itemid'];

        $itemid = parent::updateItem($args);
        return $itemid;
    }

}
class ActivityList extends DataObjectList
{
}

?>
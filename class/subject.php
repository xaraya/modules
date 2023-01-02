<?php
/**
 * Workflow Module Test Subject for Symfony Workflow tests
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Workflow Module
 * @link http://xaraya.com/index.php/release/188.html
 * @author Workflow Module Development Team
 */

class xarWorkflowSubject
{
    private $marking   = [];
    private $context   = [];

    public function getId()
    {
        return spl_object_id($this);
    }

    // See https://write.vanoix.com/alexandre/creer-un-workflow-metier-avec-le-composant-symfony-workflow
    //
    // See https://github.com/symfony/symfony/blob/6.3/src/Symfony/Component/Workflow/Tests/Subject.php
    public function getMarking()
    {
        return $this->marking;
    }

    public function setMarking($marking, array $context = [])
    {
        $this->marking = $marking;
        $this->context = $context;
    }

    public function getContext()
    {
        return $this->context;
    }
}

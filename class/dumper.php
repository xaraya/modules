<?php
/**
 * Workflow Module Graphviz Dumper for Symfony Workflow
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

use Symfony\Component\Workflow\Dumper\StateMachineGraphvizDumper;
use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\Marking;

class xarWorkflowDumper extends StateMachineGraphvizDumper
{
    protected $baseURL = '?';

    public function setBaseURL(string $workflowName, string $sitePrefix = '')
    {
        $this->baseURL = $sitePrefix . '/index.php?module=workflow&type=user&func=test&workflow=' . $workflowName;
    }

    public function formatName(string $name)
    {
        return ucwords(str_replace('_', ' ', $name));
    }

    protected function findPlaces(Definition $definition, Marking $marking = null): array
    {
        $places = parent::findPlaces($definition, $marking);
        foreach (array_keys($places) as $place) {
            $places[$place]['attributes']['href'] = $this->baseURL . '&place=' . $place;
            $places[$place]['attributes']['name'] = $this->formatName($place);
        }
        return $places;
    }

    protected function findEdges(Definition $definition): array
    {
        $edges = parent::findEdges($definition);
        foreach (array_keys($edges) as $from) {
            $edgelist = [];
            foreach ($edges[$from] as $edge) {
                $edge['attributes']['href'] = $this->baseURL . '&transition=' . $edge['name'];
                $edge['name'] = $this->formatName($edge['name']);
                $edgelist[] = $edge;
            }
            $edges[$from] = $edgelist;
        }
        return $edges;
    }
}

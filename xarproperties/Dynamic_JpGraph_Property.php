<?php
/**
 * Property for JpGraph
 *
 * @package modules
 * @copyright (C) 2006-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage JpGraph Module
 * @link http://xaraya.com/index.php/release/819.html
 * @author JpGraph Module Development Team
 */
/**
 * @author MichelV <michelv@xaraya.com>
 * @author Marc Lutolf <mfl@netspan.ch>
 */
$defines = xarModApiFunc('jpgraph','admin','defines');
include_once "modules/base/xarproperties/Dynamic_Image_Property.php";
include_once "modules/jpgraph/xarincludes/jpgraph2/jpgraph.php";

class Dynamic_JpGraph_Property extends Dynamic_Image_Property
{

    public $window_width = 300;
    public $window_height = 300;

    public $graph_type = 'line';
    /**
     * Set the property; constructor
     *
     * @return
     **/
    function Dynamic_JpGraph_Property($args)
    {
        $this->Dynamic_Image_Property($args);

        $vars = array(
            'window_width',
            'window_height',
            'margin_top',
            'margin_bottom',
            'margin_left',
            'margin_right',
            'graph_title',
            'graph_type',
            'graph_xtitle',
            'graph_ytitle',
            'graph_shadow',
            'plot_legend',
            'plot_weight',
            'plot_color',
            'plot_style',
            'plot_stepstyle',
            'value_show',
            'value_color',
            'value_format',
            'xaxis_color',
            'xaxis_weight',
            'yaxis_color',
            'yaxis_weight',
            'mark_type',
        );

        foreach ($vars as $var) {
            if (isset($args[$var])) {
                $this->$var = $args[$var];
            }
            elseif (!isset($this->$var)) {
                $this->$var = xarModGetVar('jpgraph', $var);
            }
        }

        if (empty($this->ydata)) {
            $this->ydata = array(11,3,8,12,5,1,9,13,5,7);
        }
        // Create the graph. These two calls are always required
       // var_dump($this->window_width);
        $this->graph = new Graph($this->window_width,300,"auto",xarModGetVar('jpgraph', 'cachetimeout'));
        $this->graph->SetScale("textlin");
        $this->graph->img->SetMargin($this->margin_left,$this->margin_right,$this->margin_top,$this->margin_bottom);
        $this->graph->title->Set($this->graph_title);
        $this->graph->SetShadow($this->graph_shadow);
//      $this->graph->legend->Pos(0.05,0.5,"right","center");
//      $this->graph->legend->SetLayout(LEGEND_HOR);
//      $this->graph->SetScale("textlin");

        $this->graph->xaxis->SetFont(FF_FONT1,FS_BOLD);
        $this->graph->xaxis->title->Set($this->graph_xtitle);
//      $this->graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
//      $this->graph->xaxis->SetPos("min");
        $this->graph->xaxis->SetColor($this->xaxis_color);
        $this->graph->xaxis->SetWeight($this->xaxis_weight);
//      $this->graph->xaxis->scale->SetGrace(10,10);
//      $this->graph->xgrid->Show(true,false);
//      $this->graph->xaxis->scale->ticks->SupressFirst();
//      $a = $gDateLocale->GetShortMonth();
//      $this->graph->xaxis->SetTickLabels($a);
//      $this->graph->xaxis->SetTextLabelInterval(2);
//      $this->graph->xaxis->SetLabelAngle(90);

        $this->graph->yaxis->SetFont(FF_FONT1,FS_BOLD);
        $this->graph->yaxis->title->Set($this->graph_ytitle);
//      $this->graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
        $this->graph->yaxis->SetColor($this->yaxis_color);
        $this->graph->yaxis->SetWeight($this->yaxis_weight);
//      $this->graph->yaxis->scale->SetGrace(10,10);
//      $this->graph->ygrid->Show(true,true);
//      $this->graph->yaxis->scale->ticks->SupressFirst();
//      $a = $gDateLocale->GetShortMonth();
//      $this->graph->yaxis->SetTickLabels($a);
//      $this->graph->yaxis->SetTextLabelInterval(2);
//      $this->graph->yaxis->SetLabelAngle(90);

        switch ($this->graph_type) {
            case 'bar':
            default:
                include_once "modules/jpgraph/xarincludes/jpgraph2/jpgraph_bar.php";
                $plot=new BarPlot($this->ydata);
            break;
            case 'gantt':
                include_once "modules/jpgraph/xarincludes/jpgraph2/jpgraph_gantt.php";
            break;
            case 'stock':
                include_once "modules/jpgraph/xarincludes/jpgraph2/jpgraph_stock.php";
            break;
            case 'error':
                include_once "modules/jpgraph/xarincludes/jpgraph2/jpgraph_error.php";
                $plot=new ErrorPlot($this->ydata);
            break;
            case 'error':
                include_once "modules/jpgraph/xarincludes/jpgraph2/jpgraph_error.php";
                include_once "modules/jpgraph/xarincludes/jpgraph2/jpgraph_line.php";
                $plot=new ErrorLinePlot($this->ydata);
            break;
            case 'line':
                include_once "modules/jpgraph/xarincludes/jpgraph2/jpgraph_line.php";
                $plot=new LinePlot($this->ydata);
                $plot->mark->SetType($this->mark_type);
                $plot->SetStepStyle($this->plot_stepstyle);
                $plot->SetStyle($this->plot_style);
                //$lineplot->mark->SetType(MARK_CIRCLE);
            break;
        }


        $plot->SetLegend($this->plot_legend);
        $plot->SetColor($this->plot_color);
        $plot->SetWeight($this->plot_weight);
        $plot->value->Show($this->value_show);
        $plot->value->SetColor($this->value_color);
        $plot->value->SetFont(FF_FONT1,FS_BOLD);
        $plot->value->SetFormat($this->value_format);
        // Style can also be specified as SetStyle([1|2|3|4]) or
        // SetStyle("solid"|"dotted"|"dashed"|"lobgdashed")

        // Add the plot to the graph
        $this->graph->Add($plot);

    }
    /**
     * Show the actual output of the graph-plot generated
     */

    function showOutput($data = array())
    {
        //TODO: is there a better way? We should send directly to the browser rather than to a file

        $data['value'] = 'var/cache/jpgraph/imageoutput'.microtime().'.png';
        $image = $this->graph->Stroke($data['value']);

       // return parent::showOutput($data);
        return xarTplProperty('jpgraph', 'graph', 'showoutput', $data);
        unset($data);
    }
    /**
     * Get the base information for this property.
     *
     * @return array Base information for this property
     **/
     function getBasePropertyInfo()
     {
         $args = array();
         $baseInfo = array(
                              'id'         => 191,
                              'name'       => 'JpGraph',
                              'label'      => 'JpGraph property',
                              'format'     => '191',
                              'validation' => '',
                              'source'     => '',
                              'dependancies' => '',
                              'requiresmodule' => 'jpgraph',
                              'aliases' => '',
                              'args'       => serialize( $args ),
                            /* ... */
                           );
        return $baseInfo;
    }
}
?>

<?php
/**
 * Add new item
 *
 * @package modules
 * @copyright (C) 2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage JpGraph Module
 * @link http://xaraya.com/index.php/release/819.html
 * @author JpGraph Module Development Team
 */
/**
 * Add new item
 *
 * This is a standard function that is called whenever an administrator
 * wishes to create a new module item
 *
 * @author JpGraph module development team
 * @return array
 */
function jpgraph_admin_new($args)
{
    /* Admin functions of this type can be called by other modules. If this
     * happens then the calling module will be able to pass in arguments to
     * this function through the $args parameter. Hence we extract these
     * arguments *before* we have obtained any form-based input through
     * xarVarFetch().
     */
    extract($args);

    /* Get parameters from whatever input we need. All arguments to this
     * function should be obtained from xarVarFetch(). xarVarFetch allows
     * the checking of the input variables as well as setting default
     * values if needed. Getting vars from other places such as the
     * environment is not allowed, as that makes assumptions that will
     * not hold in future versions of Xaraya
     */
    if (!xarVarFetch('img',  'int', $img,  false,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('name',    'str:1:', $name,    $name,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'array',  $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;
    /* Initialise the $data variable that will hold the data to be used in
     * the blocklayout template, and get the common menu configuration - it
     * helps if all of the module pages have a standard menu at the top to
     * support easy navigation
     */
    $data = xarModAPIFunc('jpgraph', 'admin', 'menu');
    /* Security check - important to do this as early as possible to avoid
     * potential security holes or just too much wasted processing
     */
  //  if (!xarSecurityCheck('AddJpGraph')) return;

    $x_series = array(1,2,3,4);

    // The 'remas average' series is always shown.
    // (except for the scatter diagram, pepem)

    $series['remas'] = array(
        'data' => array (3,6,4,8),
        'legend' => xarML('remas Average'),
        'legend2' => xarML('remas Sample'),
        'title' => xarML('remas Average'),
        'title2' => xarML('remas Sample'),
        'colour' => 'darkorchid4', // TODO: centralise and standardise colours
    );
    $data['series'] = $series;
    $data['x_series'] = $x_series;


    if ($img == 1) {
        // Start up the graphing functions.
        include ("modules/jpgraph/xarincludes/jpgraph2/jpgraph.php");
        include ("modules/jpgraph/xarincludes/jpgraph2/jpgraph_bar.php");
        //Example below: QPL aditus.nu
        // Some data
        $databary=array(12,7,16,5,7,14,9,3);

        // New graph with a drop shadow
        $graph = new Graph(300,200,'auto');
        $graph->SetShadow();

        // Use a "text" X-scale
        $graph->SetScale("textlin");

        // Set title and subtitle
        $graph->title->Set("Elementary barplot with a text scale");

        // Use built in font
        $graph->title->SetFont(FF_FONT1,FS_BOLD);

        // Create the bar plot
        $b1 = new BarPlot($databary);
        $b1->SetLegend("Temperature");
        //$b1->SetAbsWidth(6);
        //$b1->SetShadow();

        // The order the plots are added determines who's ontop
        $graph->Add($b1);

        // Finally output the  image
        $graph->Stroke();
        // End example
        exit();
    }

 //   $data['graph'] = xarModAPIFunc('jpgraph','admin','bargraphex',array('x_series'=>$x_series, 'series'=>$series, 'disp'=>'attachment'));
    /* Return the template variables defined in this function */
    return $data;
}
?>
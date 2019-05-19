<?php
/**
 * Scraper Module
 *
 * @package modules
 * @subpackage scraper
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2019 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Scrape a URL
 *
 */

sys::import('composer.vendor.autoload');
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;

sys::import('modules.dynamicdata.class.objects.master');

function scraper_adminapi_scrape($args)
{
    if (!isset($args['id'])) return false;

    // Get the URL and code for this ID
    $charge = DataObjectMaster::getObject(array('name' => 'scraper_urls'));
    $charge->getItem(array('itemid' => $args['id']));
    
    // Create a Goutte client
    $client = new Client();
    
    // Add any initialization options here
    $guzzleClient = new GuzzleClient(array(
//        'timeout' => 60,
    ));
    $client->setClient($guzzleClient);
    
    // Add the URL
    $crawler = $client->request('GET', $charge->properties['url']->value);
    
    // Execute the scraping code
    $code = $charge->properties['code']->value;
    $data = eval($code);
    // By convention we will return an array
    if (empty($data)) $data = array();

/*$crawler->filter('.eds-l-pad-all-1')->each(function($node){
 
  $node->filter('.event-card__formatted-name--is-clamped')->each(function($nodeTitle){
    echo "{";
    echo '"event_title" :"' . $nodeTitle->text() . '"<br/>';
  });

  $node->filter('.eds-media-card-content__sub-content')->each(function($nodeData){
    echo "{";
    echo '"event_time_place" :"' . $nodeData->html() . '"<br/>';
    echo "},";
  });

});
*/

    return $data;
}
?>
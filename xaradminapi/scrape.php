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

function scraper_adminapi_scrape($args)
{
    if (!isset($args['url'])) return false;

    // Create a Goutte client
    $client = new Client();
    
    // Add any initialization options here
    $guzzleClient = new GuzzleClient(array(
//        'timeout' => 60,
    ));
    $client->setClient($guzzleClient);
    
    // Add the URL
    $crawler = $client->request('GET', 'https://www.eventbrite.com/d/ca--sacramento/events/');
    
$crawler->filter('.eds-l-pad-all-l')->each(function($node){
  
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

    return true;
}
?>
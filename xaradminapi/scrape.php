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
 * @param integer $id The ID of the
 * @return array $data
 *
 * To test this, call
 * $data = xarMod::apiFunc('scraper', 'admin', 'scrape', array('id' => 1));
 */

sys::import('composer.vendor.autoload');
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;

sys::import('modules.dynamicdata.class.objects.master');

function scraper_adminapi_scrape($args)
{
    if (!isset($args['id'])) {
        return false;
    }

    // Get the URL and code for this ID
    $charge = DataObjectMaster::getObject(['name' => 'scraper_urls']);
    $charge->getItem(['itemid' => $args['id']]);

    // Create a Goutte client
    $client = new Client();

    // Add any initialization options here
    $guzzleClient = new GuzzleClient([
//        'timeout' => 60,
    ]);
    $client->setClient($guzzleClient);

    // Add the URL
    // IMPORTANT: by convention the crawling object should always be called "$crawler"
    $crawler = $client->request('GET', $charge->properties['url']->value);

    // Execute the scraping code
    $code = $charge->properties['code']->value;
    eval($code);
    // By convention we will return an array
    if (empty($data)) {
        $data = [];
    }

    return $data;
}

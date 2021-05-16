<?php

namespace Drupal\discogs_lookup;

use Drupal\Core\Url;
use Drupal\Core\Cache\Cache;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Cache\CacheBackendInterface;
use GuzzleHttp\Client;


 class DiscogsLookupService {
  public function _discogs_api_get_query($uri) {
    //$cache = $this->_spotify_api_get_cache_search($uri);
    $search_results = null;

    $options = array(
      'method' => 'GET',
      'timeout' => 3,
      'headers' => array(
        'Accept' => 'application/json',
        'Authorization' => 'Discogs token=wQeuTSmwtjhtzBqvmmYbDztEUtkNfGoYNMxyDjDG',
        'User-Agent'=> 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:88.0) Gecko/20100101 Firefox/88.0',
      ),
    );

  
    $search_results = \Drupal::httpClient()->get($uri);
      

    if (empty($search_results->error)) {
        $search_results = json_decode($search_results->getBody(), TRUE);
        //$this->_spotify_api_set_cache_search($uri, $search_results);
  
    }
    else {
        \Drupal::messenger()->addMessage(t('The search request resulted in the following error: @error.') . array('@error' => $search_results->error,)
        );
  
        return $search_results->error;
    }
    
    return $search_results;
  }
 }
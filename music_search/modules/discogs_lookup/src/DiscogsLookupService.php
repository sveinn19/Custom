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
  
    $uri = $uri . '&token=wQeuTSmwtjhtzBqvmmYbDztEUtkNfGoYNMxyDjDG';
  
    $search_results = \Drupal::httpClient()->get($uri, $options);
      

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
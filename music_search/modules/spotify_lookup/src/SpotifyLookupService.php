<?php

namespace Drupal\spotify_lookup;

use Drupal\Core\Url;
use Drupal\Core\Cache\Cache;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Cache\CacheBackendInterface;
use GuzzleHttp\Client;

class SpotifyLookupService {
/**
 * Sends a GET query to Spotify for specific URL
 *
 * @param $uri string
 *   The fully generated search string
 * @return object
 *   Returns a stdClass with the search results or an error message
 */

  public function _spotify_api_get_query($uri) {
      $cache = $this->_spotify_api_get_cache_search($uri);
      $search_results = null;
    
      if (!empty($cache)) {
        $search_results = $cache;
      }
      else {
        $token = $this->_spotify_api_get_auth_token();
        $token = json_decode($token);
        $options = array(
          'method' => 'GET',
          'timeout' => 3,
          'headers' => array(
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token->access_token,
          ),
        );
    
        $search_results = \Drupal::httpClient()->get($uri, $options);
        

        if (empty($search_results->error)) {
          $search_results = json_decode($search_results->getBody(), TRUE);
          $this->_spotify_api_set_cache_search($uri, $search_results);
    
        }
        else {
          \Drupal::messenger()->addMessage(t('The search request resulted in the following error: @error.') . array('@error' => $search_results->error,)
          );
    
          return $search_results->error;
        }
      }
    
      return $search_results;
    }

    


  
  /**
   * Saves a search to Drupal's internal cache.
   *
   * @param string $cid
   *   The cache id to use.
   * @param array $data
   *   The data to cache.
   */
  public function _spotify_api_set_cache_search($cid, array $data) {
    //cache_set($cid, $data, 'spotify-api-cache', time() + SPOTIFY_CACHE_LIFETIME);
    \Drupal::cache()
      ->set($cid, $data, CacheBackendInterface::CACHE_PERMANENT);

    //$cache->set($cid, $data, CacheBackendInterface::CACHE_PERMANENT, 'spotify-api-cache');
  }
  
  /**
   * Looks up the specified cid in cache and returns if found
   *
   * @param string $cid
   *   Normally a uri with a search string
   *
   * @return array|bool
   *   Returns either the cache results or false if nothing is found.
   */
  public function _spotify_api_get_cache_search($cid) {

    //$cache = cache_get($cid, 'spotify-api-cache');
    $cache = \Drupal::cache()->get($cid);

    if (!empty($cache)) {
      if ($cache->expire > time()) {
        return $cache->data;
      }
    }
    return FALSE;
  }
  
  /**
   * Gets Auth token from the Spotify API
   */
  public function _spotify_api_get_auth_token() {
    $connection_string = "https://accounts.spotify.com/api/token";
    //$key = base64_encode(SPOTIFY_API_CLIENT_ID . ':' . SPOTIFY_API_CLIENT_SECRET);
    $key = base64_encode('0179a2a0b37440a191eb43966c770e39' . ':' . 'acac856d0bb54643b8869c49fb5c95dc');
    $ch = curl_init();
  
    curl_setopt($ch, CURLOPT_URL, $connection_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_POST, 1);
  
    $headers = array();
    $headers[] = 'Authorization: Basic ' . $key;
    $headers[] = "Content-Type: application/x-www-form-urlencoded";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  
    $result = curl_exec($ch);
  
    curl_close ($ch);
    return $result;
  }
}
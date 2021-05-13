<?php
namespace Drupal\music_search\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\Markup;
use Symfony\Component\DependencyInjection\ContainerInterface;
//use Drupal\music_search\Spotify_lookup\SpotifyLookupService;
/**
 * Controller for the salutation message.
 */
class MusicSearchController extends ControllerBase {
  /**
   * MusicSearch
   *
   * @return array
   *   Our message.
   */

  //protected $spotify_service;


  public function __construct($spotify_service) {  
    $this->spotify_service = $spotify_service;
    }

    /** * {@inheritdoc} 
     * */
  public static function create(ContainerInterface $container) { 
     return new static(    
       $container->get('spotify_lookup.spotify_lookup')  
       );
     }

  public function musicSearchPrintFunc() {
    // $form = \Drupal::formBuilder()->getForm('Drupal\music_search\Form\MusicSearchForm');

    // return $form;

    // $type = \Drupal::request()->query->get('type');
    $sstring = \Drupal::routeMatch()->getParameter('string');
    $type = \Drupal::routeMatch()->getParameter('type');
  //  $request->attributes->get('_raw_variables')->get('user')

    //$test->_spotify_api_get_query('ABBA');
    $uri = 'https://api.spotify.com/v1/search?q=' . $sstring . '&' . 'type=' . $type;

    // return [
    //   // '#markup' => $this->t('name= '). t($sstring). t(' type= ') . t($type),
    //   '#markup' => http_build_query($this->spotify_service->_spotify_api_get_query($uri)), //_spotify_api_get_query($sstring),
    // ];

    return [
      // '#markup' => $this->t('name= '). t($sstring). t(' type= ') . t($type),
      '#markup' => '<h2>'. t('Results from spotify:') . '</h2></br>' . "<pre>".print_r($this->spotify_service->_spotify_api_get_query($uri), true)."</pre>",
      //http_build_query($this->spotify_service->_spotify_api_get_query($uri)), //_spotify_api_get_query($sstring),
    ];

    // return $this->spotify_service->_spotify_api_get_query('https://api.spotify.com/v1/search?q=abba&type=album');


  }

}

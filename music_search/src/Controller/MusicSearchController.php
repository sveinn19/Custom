<?php
namespace Drupal\music_search\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\Markup;
use Symfony\Component\DependencyInjection\ContainerInterface;
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

    $sstring = \Drupal::routeMatch()->getParameter('string');
    $type = \Drupal::routeMatch()->getParameter('type');
 
    $uri = 'https://api.spotify.com/v1/search?q=' . $sstring . '&' . 'type=' . $type;

    $result = $this->spotify_service->_spotify_api_get_query($uri);
    $_SESSION['s1'] = $result;
    $_SESSION['s2'] = $type;

    $form = \Drupal::formBuilder()->getForm('Drupal\music_search\Form\ResultForm');
    
    return $form;
  }

  public function createContentForm() {

    $form = \Drupal::formBuilder()->getForm('Drupal\music_search\Form\CreateContentForm');
    
    return $form;

  }

}

<?php
namespace Drupal\music_search\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\Markup;
<<<<<<< HEAD
use /home/magnusarni/website/ru/Tónlistarskráningarkerfi/web/modules/Custom/music_search/modules/Spotify_lookup/src/SpotifyLookupService.php::SpotifyLookupService;
=======
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\music_search\modules\Spotify_lookup\SpotifyLookupService;
>>>>>>> eae23d1745cc717f596062b538aeee9b58cb3609
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

  protected $spotify_service;


  public function __construct($spotify_service) {  
    $this->spotify_service = $spotify_service;
    }

    /** * {@inheritdoc} 
     * */
  public static function create(ContainerInterface $container) { 
     return new static(    
       $container->get('Spotify_lookup.lookup')  
       );
     }

  public function musicSearchPrintFunc() {
    // $form = \Drupal::formBuilder()->getForm('Drupal\music_search\Form\MusicSearchForm');

    // return $form;

    // $type = \Drupal::request()->query->get('type');
    $sstring = \Drupal::routeMatch()->getParameter('string');
    $type = \Drupal::routeMatch()->getParameter('type');
  //  $request->attributes->get('_raw_variables')->get('user')

    return [
      // '#markup' => $this->t('name= '). t($sstring). t(' type= ') . t($type),
      '#markup' => $this->spotify_service->_spotify_api_get_query($sstring),
    ];

  }

}

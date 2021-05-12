<?php
namespace Drupal\music_search\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\Markup;
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
  public function musicSearchPrintFunc(FormStateInterface $form_state) {
    // $form = \Drupal::formBuilder()->getForm('Drupal\music_search\Form\MusicSearchForm');

    // return $form;

    return [
      '#markup' => $form_state->getValue('sstring'),
    ] ;

  }

}

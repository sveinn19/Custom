<?php
namespace Drupal\music_search\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\Markup;
/**
 * Controller for the salutation message.
 */
class MusicSearchController extends ControllerBase {
  /**
   * Hello World.
   *
   * @return array
   *   Our message.
   */
  public function musicSearchPrintFunc() {
    // $form = \Drupal::formBuilder()->getForm('Drupal\music_search\Form\SearchMusicForm');

    return [
      '#markup' => $this->t('This will be our music search page.'),
    ];
  }

  public function getUserInput() {
    $output = '';
    $output .= '<form method="post">';
    $output .= '<input type="text" name="sstring">';
    $output .= '<input type="submit">';
    $output .= '</form>';
    console.log("texti");
    
    return [
      '#markup' => Markup::create($output),
    ];

  }
}

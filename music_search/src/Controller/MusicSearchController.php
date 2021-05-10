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
    return [
      '#markup' => $this->t('This will be our music search page.'),
    ];
  }

  public function getUserInput() {
    $output = '';
    $output .= '<input type="text" >';

    return [
      '#markup' => Markup::create($output),
    ];


  }
}

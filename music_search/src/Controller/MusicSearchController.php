<?php
namespace Drupal\music_search\Controller;
use Drupal\Core\Controller\ControllerBase;
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
}

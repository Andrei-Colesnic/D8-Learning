<?php

namespace Drupal\firstpage\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller for first page.
 */
class FirstpageController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function content() {
    $name = $this->currentUser()->getAccountName();
    $build = array(
      '#type'   => 'markup',
      '#markup' => t("Hello, {$name}"),
    );
    return $build;
  }

}

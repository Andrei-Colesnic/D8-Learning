<?php

/**
 * @file
 * Contains \Drupal\firstpage\Plugin\Block\HelloBlock1
 */
namespace Drupal\firstpage\Plugin\Block;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Hello' block.
 *
 * @Block(
 *   id = "hello_block",
 *   admin_label = @Translation("Hello block"),
 *   category = @Translation("Custom hello block")
 * )
 */
class CustomHelloBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $name = \Drupal::currentUser()->getAccountName();
    return array(
      '#type' => 'markup',
      '#markup' => "Greetings {$name}" ,
    );
  }
}

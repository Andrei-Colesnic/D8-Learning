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
 *   admin_label = @Translation("Hello block1"),
 *   category = @Translation("Custom hello block1")
 * )
 */
class HelloBlock1 extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $name = \Drupal::currentUser()->getAccountName();
    return array(
      '#type' => 'markup',
      '#markup' => "Greetings {$name}" ,
    );
    $a ='sdfsdfsdg';
  }
}

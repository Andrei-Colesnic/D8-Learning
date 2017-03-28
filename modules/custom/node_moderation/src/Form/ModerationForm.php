<?php
/**
 * @file
 * Contains \Drupal\node_moderation\Form\ModerationForm.
 */

namespace Drupal\node_moderation\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\node\Entity\Node;

/**
 * Sitename change form.
 */
class ModerationForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'moderation_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $options = array();
    $nodes = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadByProperties(['type' => 'article']);
    foreach ($nodes as $node) {
      $options[$node->id()] = $node->getTitle();
    }
    $form['article_title'] = array(
      '#type'     => 'select',
      '#title'    => t('Select article by name'),
      '#options'  => $options,
      '#required' => TRUE,
    );
    $form['status'] = array(
      '#name'    => 'status',
      '#type'    => 'select',
      '#title'   => t('Status'),
      '#options' => [
        '1' => 'Published',
        '0' => 'Unpublished',
      ],
    );
    $form['sticky'] = array(
      '#name'    => 'sticky',
      '#type'    => 'select',
      '#title'   => t('Sticky'),
      '#options' => [
        '1' => 'Sticky',
        '0' => 'Non Sticky',
      ],
    );
    $form['update'] = array(
      '#type'   => 'submit',
      '#value'  => t('Update'),
      '#submit' => array('::UpdateSubmitHandler'),
    );
    $form['delete'] = array(
      '#type'   => 'submit',
      '#value'  => t('Delete'),
      '#submit' => array('::DeleteSubmitHandler'),
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * Custom submission handler for updating a node.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function UpdateSubmitHandler(array &$form, FormStateInterface $form_state) {
    $node_id = $form_state->getValue('article_title');
    $node = Node::load($node_id);
    if ($form_state->getValue('sticky') == 1) {
      $node->setSticky(NODE_STICKY);
      $node->save();
    }
    else {
      $node->setSticky(NODE_NOT_STICKY);
      $node->save();
    }
    if ($form_state->getValue('status') == 1) {
      $node->setPublished(NODE_PUBLISHED);
      $node->save();
    }
    else {
      $node->setPublished(NODE_NOT_PUBLISHED);
      $node->save();
    }
  }

  /**
   * Custom submission handler for deleting a node.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function DeleteSubmitHandler(array &$form, FormStateInterface $form_state) {
    $node_id = $form_state->getValue('article_title');
    $node = Node::load($node_id);
    $node->delete();
  }
}

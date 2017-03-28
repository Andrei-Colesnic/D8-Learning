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
 * Moderation change form.
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
        NODE_PUBLISHED => 'Published',
        NODE_NOT_PUBLISHED => 'Unpublished',
      ],
    );
    $form['sticky'] = array(
      '#name'    => 'sticky',
      '#type'    => 'select',
      '#title'   => t('Sticky'),
      '#options' => [
        NODE_STICKY => 'Sticky',
        NODE_NOT_STICKY => 'Non Sticky',
      ],
    );
    $form['update'] = array(
      '#type'   => 'submit',
      '#value'  => t('Update'),
      '#submit' => array('::updateSubmitHandler'),
    );
    $form['delete'] = array(
      '#type'   => 'submit',
      '#value'  => t('Delete'),
      '#submit' => array('::deleteSubmitHandler'),
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
//  We're not using this method and it's purely implemented as required by interface
  }

  /**
   * Custom submission handler for updating a node.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function updateSubmitHandler(array &$form, FormStateInterface $form_state) {
    $node_id = $form_state->getValue('article_title');
    $node = Node::load($node_id);
    $node->setSticky($form_state->getValue('sticky'));
    $node->setPublished($form_state->getValue('status'));
    $node->save();
  }

  /**
   * Custom submission handler for deleting a node.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function deleteSubmitHandler(array &$form, FormStateInterface $form_state) {
    $node_id = $form_state->getValue('article_title');
    $node = Node::load($node_id)->delete();
  }
}

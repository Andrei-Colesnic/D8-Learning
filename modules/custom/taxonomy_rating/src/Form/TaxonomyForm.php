<?php
/**
 * @file
 * Contains \Drupal\taxonomy_rating\Form\TaxonomyForm.
 */

namespace Drupal\taxonomy_rating\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;

/**
 * Configure values for Taxonomy Rating.
 */
class TaxonomyForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'taxonomy_rating_config';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['taxonomy_rating.multiplier'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('taxonomy_rating.multiplier');
    $form['node_multiplier'] = array(
      '#type'          => 'textfield',
      '#title'         => $this->t('Node config multiplier'),
      '#required'      => TRUE,
      '#default_value' => $config->get('node_multiplier'),
    );
    $form['comment_multiplier'] = array(
      '#type'          => 'textfield',
      '#title'         => $this->t('Comment config multiplier'),
      '#required'      => TRUE,
      '#default_value' => $config->get('comment_multiplier'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (!is_numeric($form_state->getValue('node_multiplier'))) {
      $form_state->setErrorByName('node_multiplier',
        $this->t('Node Multiplier is invalid.'));
    }
    if (!is_numeric($form_state->getValue('comment_multiplier'))) {
      $form_state->setErrorByName('comment_multiplier',
        $this->t('Comment Multiplier is invalid.'));
    }

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('taxonomy_rating.multiplier')
      ->set('node_multiplier', $form_state->getValue('node_multiplier'))
      ->set('comment_multiplier', $form_state->getValue('comment_multiplier'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}

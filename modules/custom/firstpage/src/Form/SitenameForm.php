<?php
/**
 * @file
 * Contains \Drupal\firstpage\Form\SitenameForm.
 */

namespace Drupal\firstpage\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;

/**
 * Sitename change form.
 */
class SitenameForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'first_page_firstpage';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $site_name = \Drupal::config('system.site')->get('name');
    $form['title'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Site old name'),
      '#disabled'      => TRUE,
      '#default_value' => $site_name,
    );
    $form['new_name'] = array(
      '#type'     => 'textfield',
      '#title'    => t('Site new name'),
      '#required' => TRUE,
    );
    $form['submit'] = array(
      '#type'  => 'submit',
      '#value' => t('Submit'),
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Validate video URL.
    $site_old_name = \Drupal::config('system.site')->get('name');
    $site_new_name = $form_state->getValue('new_name');
    if ((strlen($site_new_name)) < 5 || ($site_new_name == $site_old_name)) {
      $form_state->setErrorByName('new_name', $this->t("Site name is invalid.",
        array('%new_name' => $form_state->getValue('new_name'))));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.
    $value = $form_state->getValue('new_name');
    \Drupal::configFactory()->getEditable('system.site')->set('name', $value)->save();
  }
}

<?php

/**
 * Contains \Drupal\sermepa_payment\Plugin\Payment\MethodConfiguration\Sermepa.
 */

namespace Drupal\sermepa_payment\Plugin\Payment\MethodConfiguration;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;
use Drupal\payment\Plugin\Payment\MethodConfiguration\Basic;

/**
 * Provides the configuration for the Sermepa payment method plugin.
 *
 * @PaymentMethodConfiguration(
 *   description = @Translation("Sermepa payment method type."),
 *   id = "sermepa_payment",
 *   label = @Translation("Sermepa")
 * )
 */
class Sermepa extends Basic {

  /**
   * Gets the setting for the production server.
   *
   * @return bool
   */
  public function isProduction() {
    return !empty($this->configuration['production']);
  }

  /**
   * Implements a form API #process callback.
   */
  public function processBuildConfigurationForm(array &$element, FormStateInterface $form_state, array &$form) {
    parent::processBuildConfigurationForm($element, $form_state, $form);

    $element['sermepa'] = [
      '#type' => 'fieldset',
      '#title' => $this->t("SERMEPA configuration")
    ];
    $element['sermepa']['production'] = [
      '#type' => 'select',
      '#title' => $this->t('Environment'),
      '#options' => array(
        "production" => $this->t("Production"),
        "test" => $this->t("Test")
      ),
      '#default_value' => $this->isProduction() ? "production" : "test",
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    $parents = $form['plugin_form']['sermepa']['#parents'];
    array_pop($parents);
    $values = $form_state->getValues();
    $values = NestedArray::getValue($values, $parents);
    $this->configuration['production'] = $values['sermepa']['production'] === "production" ? true: false;
  }

  /**
   * @return array
   */
  public function getDerivativeConfiguration() {
    return [
      'production' => $this->isProduction()
    ];
  }

}

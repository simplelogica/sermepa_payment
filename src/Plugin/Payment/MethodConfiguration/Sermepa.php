<?php

/**
 * Contains \Drupal\sermepa_payment\Plugin\Payment\MethodConfiguration\Sermepa.
 */

namespace Drupal\sermepa_payment\Plugin\Payment\MethodConfiguration;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\payment_offsite_api\Plugin\Payment\MethodConfiguration\PaymentMethodConfigurationBaseOffsite;
use CommerceRedsys\Payment\Sermepa as SermepaApi;

/**
 * Provides the configuration for the Sermepa payment method plugin.
 *
 * @PaymentMethodConfiguration(
 *   description = @Translation("Sermepa payment method type."),
 *   id = "sermepa_payment",
 *   label = @Translation("Sermepa")
 * )
 */
class Sermepa extends PaymentMethodConfigurationBaseOffsite implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return parent::defaultConfiguration() + [
      'message_text' => '',
      'message_text_format' => 'plain_text',
      'auto_submit' => true,
      'verbose' => false,
      'ipn_statuses' => [
        'success' => 'payment_success',
        'failure' => 'payment_failed',
        'pending' => 'payment_pending',
      ],
      'config' => [
        'environment' => '',
        'merchant_name' => '',
        'merchant_code' => '',
        'merchant_terminal' => '',
        'merchant_currency' => '',
        'payment_method' => 'C',
        'transaction_type' => '0',
        'encryption_key' => '',
        'url_ok' => '',
        'url_ko' => '',
      ],
    ];
  }

  /**
   * Gets the setting for the production server.
   *
   * @return string
   */
  public function getEnvironment() {
    return !empty($this->configuration['config']['environment']) ? $this->configuration['config']['environment'] : '';
  }

  /**
   * Gets the setting for the merchant name.
   *
   * @return string
   */
  public function getMerchantName() {
    return !empty($this->configuration['config']['merchant_name']) ? $this->configuration['config']['merchant_name'] : '';
  }

  /**
   * Gets the setting for the merchant code.
   *
   * @return string
   */
  public function getMerchantCode() {
    return !empty($this->configuration['config']['merchant_code']) ? $this->configuration['config']['merchant_code'] : '';
  }

  /**
   * Gets the setting for the merchant terminal.
   *
   * @return string
   */
  public function getMerchantTerminal() {
    return !empty($this->configuration['config']['merchant_terminal']) ? $this->configuration['config']['merchant_terminal'] : '';
  }

  /**
   * Gets the setting for the merchant currency.
   *
   * @return string
   */
  public function getMerchantCurrency() {
    return !empty($this->configuration['config']['merchant_currency']) ? $this->configuration['config']['merchant_currency'] : '';
  }

  /**
   * Gets the setting for the encryption key.
   *
   * @return string
   */
  public function getEncryptionKey() {
    return !empty($this->configuration['config']['encryption_key']) ? $this->configuration['config']['encryption_key'] : '';
  }

  /**
   * Gets the setting for the payment method.
   *
   * @return string
   */
  public function getPaymentMethod() {
    return !empty($this->configuration['config']['payment_method']) ? $this->configuration['config']['payment_method'] : '';
  }

  /**
   * Gets the setting for the transaction type.
   *
   * @return string
   */
  public function getTransactionType() {
    return !empty($this->configuration['config']['transaction_type']) ? $this->configuration['config']['transaction_type'] : '';
  }

  /**
   * Gets the setting for the URL OK.
   *
   * @return string
   */
  public function getUrlOK() {
    return !empty($this->configuration['config']['url_ok']) ? $this->configuration['config']['url_ok'] : '';
  }

  /**
   * Gets the setting for the URL KO.
   *
   * @return string
   */
  public function getUrlKO() {
    return !empty($this->configuration['config']['url_ko']) ? $this->configuration['config']['url_ko'] : '';
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
    $element['sermepa']['environment'] = [
      '#type' => 'select',
      '#title' => $this->t('Environment'),
      '#options' => array(
        "live" => $this->t("Live"),
        "test" => $this->t("Test")
      ),
      '#required' => TRUE,
      '#default_value' => $this->getEnvironment(),
    ];
    $element['sermepa']['merchant_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Merchant Name'),
      '#maxlength' => SermepaApi::getMerchantNameMaxLength(),
      '#required' => TRUE,
      '#default_value' => $this->getMerchantName()
    ];
    $element['sermepa']['merchant_code'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Merchant Code'),
      '#maxlength' => SermepaApi::getMerchantCodeMaxLength(),
      '#required' => TRUE,
      '#default_value' => $this->getMerchantCode()
    ];
    $element['sermepa']['merchant_terminal'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Merchant Terminal'),
      '#maxlength' => SermepaApi::getMerchantTerminalMaxLength(),
      '#required' => TRUE,
      '#default_value' => $this->getMerchantTerminal()
    ];
    $element['sermepa']['merchant_currency'] = [
      '#type' => 'select',
      '#title' => $this->t('Merchant Currency'),
      '#options' => SermepaApi::getAvailableCurrencies(),
      '#required' => TRUE,
      '#default_value' => $this->getMerchantCurrency()
    ];
    $element['sermepa']['encryption_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Encryption Key'),
      '#maxlength' => SermepaApi::getMerchantPasswordMaxLength(),
      '#required' => TRUE,
      '#default_value' => $this->getEncryptionKey()
    ];
    $element['sermepa']['payment_method'] = [
      '#type' => 'select',
      '#title' => $this->t('Payment Method'),
      '#options' => SermepaApi::getAvailablePaymentMethods(),
      '#required' => TRUE,
      '#default_value' => $this->getPaymentMethod()
    ];
    $element['sermepa']['transaction_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Transaction Type'),
      '#options' => SermepaApi::getAvailableTransactionTypes(),
      '#required' => TRUE,
      '#default_value' => $this->getTransactionType()
    ];

    $element['site_config'] = [
      '#type' => 'fieldset',
      '#title' => $this->t("Site configuration")
    ];
    $element['site_config']['url_ok'] = [
      '#type' => 'textfield',
      '#title' => $this->t('URL to redirect when OK'),
      '#required' => TRUE,
      '#default_value' => $this->getUrlOK()
    ];
    $element['site_config']['url_ko'] = [
      '#type' => 'textfield',
      '#title' => $this->t('URL to redirect when KO'),
      '#required' => TRUE,
      '#default_value' => $this->getUrlKO()
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

    $this->configuration['config']['environment'] = $values['sermepa']['environment'];
    $this->configuration['config']['merchant_name'] = $values['sermepa']['merchant_name'];
    $this->configuration['config']['merchant_code'] = $values['sermepa']['merchant_code'];
    $this->configuration['config']['merchant_terminal'] = $values['sermepa']['merchant_terminal'];
    $this->configuration['config']['merchant_currency'] = $values['sermepa']['merchant_currency'];
    $this->configuration['config']['encryption_key'] = $values['sermepa']['encryption_key'];
    $this->configuration['config']['payment_method'] = $values['sermepa']['payment_method'];
    $this->configuration['config']['transaction_type'] = $values['sermepa']['transaction_type'];

    $this->configuration['config']['url_ok'] = $values['site_config']['url_ok'];
    $this->configuration['config']['url_ko'] = $values['site_config']['url_ko'];
  }

  /**
   * @return array
   */
  public function getDerivativeConfiguration() {
    return [
      'environment' => $this->getEnvironment(),
      'merchant_name' => $this->getMerchantName(),
      'merchant_code' => $this->getMerchantCode(),
      'merchant_terminal' => $this->getMerchantTerminal(),
      'merchant_currency' => $this->getMerchantCurrency(),
      'encryption_key' => $this->getEncryptionKey(),
      'payment_method' => $this->getPaymentMethod(),
      'transaction_type' => $this->getTransactionType(),

      'url_ok' => $this->getUrlOK(),
      'url_ko' => $this->getUrlKO()
    ];
  }

}

<?php

/**
 * Contains \Drupal\sermepa_payment\Plugin\Payment\Method\Sermepa.
 */

namespace Drupal\sermepa_payment\Plugin\Payment\Method;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\payment\Entity\Payment;
use Drupal\payment\Entity\PaymentInterface;
use Drupal\Core\Url;
use Drupal\payment_offsite_api\Plugin\Payment\Method\PaymentMethodBaseOffsite;
use Drupal\payment_offsite_api\Plugin\Payment\Method\PaymentMethodOffsiteInterface;
use CommerceRedsys\Payment\Sermepa as SermepaApi;

/**
 * Sermepa payment method.
 *
 * @PaymentMethod(
 *   id = "payment_sermepa",
 *   deriver = "\Drupal\payment_offsite_api\Plugin\Payment\Method\PaymentMethodBaseOffsiteDeriver",
 *   operations_provider = "\Drupal\payment_offsite_api\Plugin\Payment\Method\PaymentMethodBaseOffsiteOperationsProvider"
 * )
 */
class Sermepa extends PaymentMethodBaseOffsite implements PaymentMethodOffsiteInterface {

  /**
   * {@inheritdoc}
   */
  public function paymentForm() {
    $form = [];
    $payment = $this->getPayment();

    // Create Sermepa object
    $gateway = $this->getSermepaGateway();

    // Original price * 100 and without decimals
    $sermepa_price = number_format($payment->getAmount(), 2, '', '');

    // Build callback URL
    $callback_url = Url::fromRoute('sermepa_payment.callback')
      ->setRouteParameters(['payment_id' => $payment->id()])
      ->setAbsolute(TRUE)
      ->toString();

    // Configure gateway
    $gateway->setOrder($payment->id());
    $gateway->setAmount($sermepa_price);
    $gateway->setMerchantUrl($callback_url);
    $gateway->setCurrency($this->pluginDefinition['config']['merchant_currency']);
    $gateway->setPaymentMethod($this->pluginDefinition['config']['payment_method']);
    $gateway->setTransactionType($this->pluginDefinition['config']['transaction_type']);
    $gateway->setUrlKO($this->pluginDefinition['config']['url_ko']);
    $gateway->setUrlOK($this->pluginDefinition['config']['url_ok']);

    // Set environment URL
    $form['#action'] = $gateway->getEnvironment();

    // Apply Sermepa parameters
    $this->addPaymentFormData('Ds_SignatureVersion', 'HMAC_SHA256_V1');
    $this->addPaymentFormData('Ds_MerchantParameters', $gateway->composeMerchantParameters());
    $this->addPaymentFormData('Ds_Signature', $gateway->composeMerchantSignature());

    // And auto submit the form
    $this->setAutoSubmit($this->pluginDefinition['auto_submit']);

    $form += $this->generateForm();

    return $form;
  }

  /**
   * Function to get a fresh instance of Sermepa Library
   */
  public function getSermepaGateway() {
    return new SermepaApi(
      $this->pluginDefinition['config']['merchant_name'],
      $this->pluginDefinition['config']['merchant_code'],
      $this->pluginDefinition['config']['merchant_terminal'],
      $this->pluginDefinition['config']['encryption_key'],
      $this->pluginDefinition['config']['environment']
    );
  }

  /**
   * {@inheritdoc}
   */
  public function ipnExecute() {
    if (!$this->ipnValidate()) {
      // @todo replace with throw exceptions.
      return [
        'status' => 'fail',
        'message' => '',
        'response_code' => 200,
      ];
    }

    $this->getPayment()->setPaymentStatus($this->paymentStatusManager->createInstance('payment_pending'));
    $this->getPayment()->save();

    return [
      'status' => 'pending',
      'message' => '',
      'response_code' => 200,
    ];
  }

  /**
   * {@inheritdoc}
   */
  function ipnValidate() {
    return true;
  }

  /**
   * {@inheritdoc}
   */
  function getSupportedCurrencies() {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  function isConfigured() {
    // It must be configured, as the required parameters are checked on the config form.
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getResultPages() {
    return [
      'pending' => TRUE,
    ];
  }
}

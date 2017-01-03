<?php

/**
 * Contains \Drupal\sermepa_payment\Plugin\Payment\Method\Sermepa.
 */

namespace Drupal\sermepa_payment\Plugin\Payment\Method;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\payment\Entity\Payment;
use Drupal\payment\Entity\PaymentInterface;
use Drupal\payment_offsite_api\Plugin\Payment\Method\PaymentMethodBaseOffsite;
use Drupal\payment_offsite_api\Plugin\Payment\Method\PaymentMethodOffsiteInterface;
use CommerceRedsys\Payment\Sermepa as SermepaApi;

/**
 * Sermepa payment method.
 *
 * @PaymentMethod(
 *   id = "sermepa_payment",
 *   label = @Translation("Sermepa"),
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

    // Configure gateway
    $gateway->setOrder($payment->id());
    $gateway->setAmount($payment->getAmount());
    $gateway->setCurrency($this->configuration['merchant_currency']);
    $gateway->setMerchantPaymentMethod($this->configuration['merchant_payment_method']);
    $gateway->setMerchantUrl(\Drupal::url('sermepa_payment.callback', ['payment_id' => $payment->id()]));
    $gateway->setUrlKO($this->configuration['url_ko']);
    $gateway->setUrlOK($this->configuration['url_ok']);

    // Set environment URL
    $form['#action'] = $gateway->getEnvironment();

    // Apply Sermepa parameters
    foreach ($gateway->getParameters() as $name => $value) {
      $this->addPaymentFormData($name, $value);
    }

    // And auto submit the form
    $this->setAutoSubmit(true);

    $form += $this->generateForm();

    return $form;
  }

  /**
   * Function to get a fresh instance of Sermepa Library
   */
  public function getSermepaGateway() {
    return new SermepaApi(
      $this->configuration['merchant_name'],
      $this->configuration['merchant_code'],
      $this->configuration['merchant_terminal'],
      $this->configuration['encryption_key'],
      $this->configuration['environment']
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
      'status' => 'success',
      'message' => '',
      'response_code' => 200,
    ];
  }

  /**
   * {@inheritdoc}
   */
  function isConfigured() {
    // It must be configured, as the required parameters are checked on the config form.
    return true;
  }

  /**
   * {@inheritdoc}
   */
  public function getResultPages() {
    return [
      'success' => FALSE,
      'fail' => TRUE,
      'pending' => FALSE,
    ];
  }
}

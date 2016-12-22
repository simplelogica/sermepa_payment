<?php

/**
 * Contains \Drupal\sermepa_payment\Plugin\Payment\Method\Sermepa.
 */

namespace Drupal\sermepa_payment\Plugin\Payment\Method;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\payment\Entity\Payment;
use Drupal\payment\Entity\PaymentInterface;
use Drupal\payment_offsite_api\Plugin\Payment\Method\PaymentMethodOffsite;
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
    $gateway = new SermepaApi(
      $this->configuration['merchant_name'],
      $this->configuration['merchant_code'],
      $this->configuration['merchant_terminal'],
      $this->configuration['encryption_key'],
      $this->configuration['environment']
    );

    // Configure gateway
    $gateway->setOrder($payment->id());
    $gateway->setAmount($payment->getAmount());
    $gateway->setCurrency($this->configuration['merchant_currency']);
    $gateway->setMerchantPaymentMethod($this->configuration['merchant_payment_method']);
    $gateway->setUrlKO(); // TODO: Add KO URL
    $gateway->setUrlOK(); // TODO: Add OK URL

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

}

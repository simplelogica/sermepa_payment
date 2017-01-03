<?php

namespace Drupal\sermepa_payment\Controller;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\payment\Entity\Payment;
use Drupal\sermepa_payment\Plugin\Payment\Method\Sermepa as SermepaMethod;

class SermepaController extends ControllerBase {

  /**
   * Checks access for a specific request.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   */
  public function access(AccountInterface $account) {
    $tempstore = \Drupal::service('user.private_tempstore')->get('sermepa_payment');

    $received_payment_id = \Drupal::request()->get('payment_id');

    // Try to load payment and the payment_method from database
    $payment = Payment::load($received_payment_id);
    $payment_method = is_null($payment) ? null : $payment->getPaymentMethod();

    // Allow access of payment IDs match, the paymet can be loaded and the
    // method used is Sermepa
    return AccessResult::allowedIf(
      !is_null($payment) &&
      !is_null($payment_method) &&
      ($payment_method instanceof SermepaMethod)
    );
  }

  /**
    * Action process Sermepa response
    *
    * @param Request $request
    *   Request data, including the step variable tat indicates current step.
    */
  public function callback(Request $request) {
    // Get payment object (which exists as checked on access policy).
    $received_payment_id = \Drupal::request()->get('payment_id');
    $payment = Payment::load($received_payment_id);

    // Get payment method and instantiate a gateway.
    $payment_method = $payment->getPaymentMethod();
    $gateway = $payment_method->getSermepaGateway();

    // Get and check feedback
    $feedback = $gateway->getFeedback();

    \Drupal::logger()->info("[SERMEPA][Payment##{$received_payment_id}]: Got feedback: #{var_dump($feedback)}");

    if ($gateway->validSignatures($feedback)) {
      $response = $gateway->decodeMerchantParameters($feedback['Ds_MerchantParameters']);
      $response_code = $response['Ds_Response'];

      \Drupal::logger()->info("[SERMEPA][Payment##{$received_payment_id}]: Decoded response: #{var_dump($response)}");

      if ($response_code <= 99) {
        \Drupal::logger()->info("[SERMEPA][Payment##{$received_payment_id}]: SUCCESSFUL response code: #{$response_code}");
        $payment->setPaymentStatus(PaymentStatus::load('payment_success'));
      } else {
        // Assign error status or a common one if not found
        \Drupal::logger()->info("[SERMEPA][Payment##{$received_payment_id}]: ERROR response code: #{$response_code}");
        $payment_status = PaymentStatus::load('payment_sermepa_error_'.$response_code);
        $payment->setPaymentStatus($payment_status ?: PaymentStatus::load('payment_sermepa_error_common'));
      }
    }
    else {
      \Drupal::logger()->error("[SERMEPA][Payment##{$received_payment_id}]: SIGNATURE ERROR on received feedback: #{var_dump($feedback)}");
    }

    return true;
  }
}

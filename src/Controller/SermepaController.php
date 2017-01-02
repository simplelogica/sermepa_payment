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

    $storedPaymentId = $tempstore->get("payment_id");
    $receivedPaymentId = \Drupal::request()->get('payment_id');

    // Try to load payment and the payment_method from database
    $payment = Payment::load($receivedPaymentId);
    $payment_method = is_null($payment) ? null : $payment->getPaymentMethod();

    // Allow access of payment IDs match, the paymet can be loaded and the
    // method used is Sermepa
    return AccessResult::allowedIf(
      ($storedPaymentId == $receivedPaymentId) &&
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
    // Do something magic.
    return [];
  }
}

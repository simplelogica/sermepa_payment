<?php

namespace Drupal\sermepa_payment\Controller;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\sermepa_payment\Event\SermepaEvent;
use Symfony\Component\HttpFoundation\Request;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Url;

use Drupal\payment\Entity\Payment as PaymentEntity;
use Drupal\payment\Payment;
use Drupal\payment\Entity\PaymentStatus;
use Drupal\sermepa_payment\Plugin\Payment\Method\Sermepa as SermepaMethod;

class SermepaController extends ControllerBase {

  /**
   * Checks access for a specific request.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   */
  public function access(AccountInterface $account) {
    $received_payment_id = \Drupal::request()->get('payment_id');

    // Try to load payment and the payment_method from database
    $payment = PaymentEntity::load($received_payment_id);
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
    * Callback for Sermepa response
    *
    * @param Request $request
    *   Request data, including the step variable tat indicates current step.
    */
  public function callback(Request $request) {
    // Get payment object (which exists as checked on access policy).
    $received_payment_id = \Drupal::request()->get('payment_id');
    $payment = PaymentEntity::load($received_payment_id);

    // Parse response (if any).
    $result = self::parseResponse($payment);

    \Drupal::service('event_dispatcher')
      ->dispatch(SermepaEvent::AFTER_CALLBACK, new SermepaEvent($payment));

    return $result;
  }

  /**
    * Success action for Sermepa payments
    *
    * @param Request $request
    *   Request data, including the step variable tat indicates current step.
    */
  public function success(Request $request) {
    // Get payment object (which exists as checked on access policy).
    $received_payment_id = \Drupal::request()->get('payment_id');
    $payment = PaymentEntity::load($received_payment_id);

    $uri = $payment->getPaymentMethod()->getPluginDefinition()['config']['url_ok'];
    return new TrustedRedirectResponse(self::buildUrl($uri));
  }

  /**
    * Failed action for Sermepa payments
    *
    * @param Request $request
    *   Request data, including the step variable tat indicates current step.
    */
  public function failed(Request $request) {
    // Get payment object (which exists as checked on access policy).
    $received_payment_id = \Drupal::request()->get('payment_id');
    $payment = PaymentEntity::load($received_payment_id);

    $uri = $payment->getPaymentMethod()->getPluginDefinition()['config']['url_ko'];
    return new TrustedRedirectResponse(self::buildUrl($uri));
  }

  /**
   * Private function to parse response (if any).
   *
   * @param Payment $payment
   *   Payment object.
   */
  private static function parseResponse(PaymentEntity &$payment) {
    // Get payment method and instantiate a gateway.
    $payment_method = $payment->getPaymentMethod();
    $gateway = $payment_method->getSermepaGateway();

    // Get and check feedback
    $feedback = $gateway->getFeedback();

    // Only process feedback if there is any
    if ($feedback != FALSE) {
      \Drupal::logger('default')->info("[SERMEPA][Payment#" . $payment->id() . "]: Got feedback: " . print_r($feedback, true));

      if ($gateway->validSignatures($feedback)) {
        $response = $gateway->decodeMerchantParameters($feedback['Ds_MerchantParameters']);
        $response_code = intval($response['Ds_Response']);

        \Drupal::logger('default')->info("[SERMEPA][Payment#" . $payment->id() . "]: Decoded response: " . print_r($response, true));

        if ($response_code <= 99) {
          \Drupal::logger('default')->info("[SERMEPA][Payment#" . $payment->id() . "]: SUCCESSFUL response code: " . $response_code);
          $payment->setPaymentStatus(Payment::statusManager()->createInstance('payment_success'));
          $payment->save();
          return TRUE;
        } else {
          // Assign error status or a common one if not found
          \Drupal::logger('default')->info("[SERMEPA][Payment#" . $payment->id() . "]: ERROR response code: " . $response_code);
          $payment_status = Payment::statusManager()->createInstance('payment_sermepa_error_'.$response_code);
          $payment_status = ($payment_status->getPluginId() !== 'payment_unknown') ? $payment_status : Payment::statusManager()->createInstance('payment_sermepa_error_common');

          // And apply it
          $payment->setPaymentStatus($payment_status);
          $payment->save();
        }
      }
      else {
        \Drupal::logger('default')->error("[SERMEPA][Payment#" . $payment->id() . "]: SIGNATURE ERROR on received feedback: " . var_dump($feedback));
      }
    }

    return FALSE; // If no TRUE was returned before, then something bad happened or no data received.
  }

  private static function buildUrl(string $url) {
    if (UrlHelper::isExternal($url)) {
      return $url;
    } else {
      $url_object = Url::fromUserInput($url);
      return $url_object->setAbsolute(TRUE)->toString(TRUE)->getGeneratedUrl();
    }
  }
}

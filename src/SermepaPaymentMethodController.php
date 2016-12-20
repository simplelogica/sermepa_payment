<?php


/**
 * Payment Controller for integration with Sermepa (RedSys) gateway.
 */
class SermepaPaymentMethodController extends PaymentMethodController {

  public $controller_data_defaults = array(
    'currency'          => '',
    'titular'           => '',
    'merchant_name'     => '',
    'merchant_code'     => '',
    'terminal'          => '',
    'signature'         => '',
    'environment'       => '',
    'encryption_method' => '',
  );

  public $payment_method_configuration_form_elements_callback = 'sermepa_payment_method_configuration';


  function __construct() {
    $this->title = 'Sermepa (RedSys)';
    $this->name = 'sermepa_payment';
    $this->description = 'Sermepa payment gateway integration';
  }


  /**
   * Implements PaymentMethodController::validate().
   */
  function validate(Payment $payment, PaymentMethod $payment_method, $strict) {
    // Empty on purpose.  For the moment there is no validation before the payment
    // execution.
  }


  /**
   * Implements PaymentMethodController::execute().
   */
  function execute(Payment $payment) {
    entity_save('payment', $payment);
    // The payment PID is stored in the session in order to check it when
    // the user is redirected to the payment submission form.  This allows us
    // to disable access to users who have not made a payment.
    $_SESSION['sermepa_payment_pid'] = $payment->pid;
    drupal_goto('sermepa/redirect/' . $payment->pid);
  }


  /**
   * Create a Sermepa object that acts as a gateway with the Sermepa TPV.
   */
  static function createGateway(Payment $payment) {
    $settings = $payment->method->controller_data;
    return new Sermepa(
      $settings['titular'],
      $settings['merchant_code'],
      $settings['terminal'],
      $settings['signature'],
      $settings['environment'],
      $settings['encryption_method'],
      array(
        'currency' => $settings['currency'],
        'merchant_name' => $settings['merchant_name'],
      ));
  }

}
<?php


/**
 * Creates the payment form that contains the payment information for the
 * Sermepa TPV.
 */
class SermepaPaymentForm {


  private $payment;
  private $transaction_type = '0';


  function __construct(Payment $payment) {
    $this->payment = $payment;
  }

  /**
   * Creates the Payment form.  This form contains all data needed to submit a
   * payment to the Sermepa TPV.  The form will autosubmit itself when rendered.
   */
  function submit($form_id) {
    $gateway = $this->createGateway();
    $form = [];
    foreach ($gateway->getFields() as $name => $value) {
      $form[$name] = array(
        '#type'   => 'hidden',
        '#value'  => $value,
      );
    }    
    $form['#action'] = url($gateway->getEnvironment(), array(
      'external' => true,
    ));
    $form['js'] = array(
        '#type' => 'markup',
        '#markup' => "<script type='text/javascript'>document.getElementById('{$form_id}').submit();</script>",
    );
    return $form;
  }

  private function createGateway() {
    $gateway = SermepaPaymentMethodController::createGateway($this->payment);
    return $gateway->setAmount($this->getAmount())
      ->setOrder($this->getOrder())
      ->setMerchantURL($this->getMerchantUrl())
      ->setUrlOK($this->getOkUrl())
      ->setUrlKO($this->getKoUrl())
      ->setTransactionType($this->transaction_type);
  }


  private function getAmount() {
    return $this->payment->totalAmount(false) * 100;
  }

  private function getMerchantUrl() {
    return url("sermepa/callback/{$this->payment->pid}", array(
      'absolute' => TRUE)
    );
  }

  private function getOkUrl() {
    return url("sermepa/return/{$this->payment->pid}", array(
      'absolute' => TRUE)
    );
  }

  private function getKoUrl() {
    return url("sermepa/failed/{$this->payment->pid}", array(
      'absolute' => TRUE
    ));
  }

  private function getOrder() {
    return substr(date('ymdHis') . '_' . $this->payment->pid, -12, 12);
  }
}
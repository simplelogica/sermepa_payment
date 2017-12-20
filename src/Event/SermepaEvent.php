<?php

namespace Drupal\sermepa_payment\Event;

use Symfony\Component\EventDispatcher\Event;

class SermepaEvent extends Event {

  const AFTER_CALLBACK = 'sermepa_payment.after_callback';

  private $payment;

  public function __construct(PaymentEntity $payment) {
    $this->payment = $payment;
  }

  public function getPayment(): PaymentEntity {
    return $this->payment;
  }
}

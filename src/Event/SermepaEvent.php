<?php

namespace Drupal\sermepa_payment\Event;

use Drupal\payment\Entity\Payment;
use Symfony\Component\EventDispatcher\Event;

class SermepaEvent extends Event {

  const AFTER_CALLBACK = 'sermepa_payment.after_callback';

  private $payment;

  public function __construct(Payment $payment) {
    $this->payment = $payment;
  }

  public function getPayment(): Payment {
    return $this->payment;
  }
}

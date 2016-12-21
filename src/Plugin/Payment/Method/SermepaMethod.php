<?php

/**
 * Contains \Drupal\sermepa_payment\Plugin\Payment\Method\Sermepa.
 */

namespace Drupal\sermepa_payment\Plugin\Payment\Method;

use Drupal\Core\Url;
use Drupal\payment\OperationResult;
use Drupal\payment\Plugin\Payment\Method\Basic;
use Drupal\payment\Response\Response;

/**
 * Sermepa payment method.
 *
 * @PaymentMethod(
 *   deriver = "\Drupal\sermepa_payment\Plugin\Payment\Method\SermepaDeriver",
 *   id = "sermepa_payment",
 *   label = @Translation("Sermepa"),
 *   operations_provider = "\Drupal\sermepa_payment\Plugin\Payment\Method\SermepaOperationsProvider",
 * )
 */
class Sermepa extends Basic {

  /**
   * @return string
   */
  public function getWebhookUrl() {
    return null;
  }

  /**
   * @return string
   */
  public function getWebhookId() {
    return null;
  }

  private function setPaymentId($paymentId) {
    $this->configuration['paymentID'] = $paymentId;
    $this->getPayment()->save();
  }

  public function getPaymentId() {
    return isset($this->configuration['paymentID']) ? $this->configuration['paymentID'] : NULL;
  }

  /**
   * @inheritDoc
   */
  public function getPaymentExecutionResult() {
    $payer = new Payer();
    $payer->setPaymentMethod('paypal');

    $itemList = new ItemList();
    $totalAmount = 0;
    foreach ($this->getPayment()->getLineItems() as $line_item) {
      $totalAmount += $line_item->getTotalAmount();

      $item = new Item();
      $item->setName($line_item->getName())
        ->setCurrency($line_item->getCurrencyCode())
        ->setQuantity($line_item->getQuantity())
        ->setPrice($line_item->getTotalAmount());
      $itemList->addItem($item);
    }

    $redirectSuccess = new Url('paypal_payment.redirect.success',
      ['payment' => $this->getPayment()->id()], ['absolute' => TRUE]);
    $redirectCancel = new Url('paypal_payment.redirect.cancel',
      ['payment' => $this->getPayment()->id()], ['absolute' => TRUE]);

    $redirectUrls = new RedirectUrls();
    $redirectUrls->setReturnUrl($redirectSuccess->toString(TRUE)->getGeneratedUrl())
      ->setCancelUrl($redirectCancel->toString(TRUE)->getGeneratedUrl());

    $amount = new Amount();
    $amount->setCurrency('USD')
      ->setTotal($totalAmount);

    $transaction = new Transaction();
    $transaction->setAmount($amount)
      ->setItemList($itemList)
      ->setDescription($this->getPayment()->id())
      ->setInvoiceNumber($this->getPayment()->id())
      ->setNotifyUrl($this->getWebhookUrl());

    $payment = new Payment();
    $payment->setIntent('sale')
      ->setPayer($payer)
      ->setRedirectUrls($redirectUrls)
      ->setTransactions([$transaction]);

    try {
      // $payment->create($this->getApiContext(self::PAYPAL_CONTEXT_TYPE_CREATE));
      // $this->setPaymentId($payment->getId());
      // $url = Url::fromUri($payment->getApprovalLink());
      // $response = new Response($url);
    } catch (\Exception $ex) {
      # TODO: clarify with the payment maintainer how we should handle Exceptions
      $response = NULL;
    }

    return new OperationResult($response);
  }

}

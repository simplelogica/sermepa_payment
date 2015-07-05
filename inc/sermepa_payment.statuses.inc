<?php


define('SERMEPA_PAYMENT_STATUS_RETURNS_CONFIRMATIONS', 'SERMEPA_PAYMENT_STATUS_RETURNS_CONFIRMATIONS');
define('SERMEPA_PAYMENT_STATUS_EXPIRED', 'SERMEPA_PAYMENT_STATUS_EXPIRED');
define('SERMEPA_PAYMENT_STATUS_EXCEPTION_CARD', 'SERMEPA_PAYMENT_STATUS_EXCEPTION_CARD');
define('SERMEPA_PAYMENT_STATUS_NOT_ALLOWED', 'SERMEPA_PAYMENT_STATUS_NOT_ALLOWED');
define('SERMEPA_PAYMENT_STATUS_INSUFFICIENT', 'SERMEPA_PAYMENT_STATUS_INSUFFICIENT');
define('SERMEPA_PAYMENT_STATUS_UNREGISTERED', 'SERMEPA_PAYMENT_STATUS_UNREGISTERED');
define('SERMEPA_PAYMENT_STATUS_SECURITY_CODE', 'SERMEPA_PAYMENT_STATUS_SECURITY_CODE');
define('SERMEPA_PAYMENT_STATUS_OUT_OF_SERVICE', 'SERMEPA_PAYMENT_STATUS_OUT_OF_SERVICE');
define('SERMEPA_PAYMENT_STATUS_OWNER_AUTHENTICATION', 'SERMEPA_PAYMENT_STATUS_OWNER_AUTHENTICATION');
define('SERMEPA_PAYMENT_STATUS_NO_REASONS', 'SERMEPA_PAYMENT_STATUS_NO_REASONS');
define('SERMEPA_PAYMENT_STATUS_WRONG_EXPIRATION', 'SERMEPA_PAYMENT_STATUS_WRONG_EXPIRATION');
define('SERMEPA_PAYMENT_STATUS_FRAUD', 'SERMEPA_PAYMENT_STATUS_FRAUD');
define('SERMEPA_PAYMENT_STATUS_ISSUER_UNAVAILABLE', 'SERMEPA_PAYMENT_STATUS_ISSUER_UNAVAILABLE');
define('SERMEPA_PAYMENT_STATUS_ORDER_DUPLICATED', 'SERMEPA_PAYMENT_STATUS_ORDER_DUPLICATED');
define('SERMEPA_PAYMENT_STATUS_TRANSACTION_REFUSED', 'SERMEPA_PAYMENT_STATUS_TRANSACTION_REFUSED');


/**
 * Implements hook_payment_status_info().
 * Defines error status for Sermepa payments.
 */
function sermepa_payment_payment_status_info() {
  return [
    new PaymentStatusInfo([
      'parent'  => PAYMENT_STATUS_FAILED,
      'status'  => SERMEPA_PAYMENT_STATUS_RETURNS_CONFIRMATIONS,
      'title'   => t('Transaction authorized for returns and confirmations'),
      'description'   => t('Transaction authorized for returns and confirmations'),
    ]),
    new PaymentStatusInfo([
      'parent'  => PAYMENT_STATUS_FAILED,
      'status'  => SERMEPA_PAYMENT_STATUS_EXPIRED,
      'title'   => t('Expired card'),
      'description'   => t('Expired card'),
    ]),
    new PaymentStatusInfo([
      'parent'  => PAYMENT_STATUS_FAILED,
      'status'  => SERMEPA_PAYMENT_STATUS_EXCEPTION_CARD,
      'title'   => t('Temporary exception card or on suspicion of fraud'),
      'description'   => t('Temporary exception card or on suspicion of fraud'),
    ]),
    new PaymentStatusInfo([
      'parent'  => PAYMENT_STATUS_FAILED,
      'status'  => SERMEPA_PAYMENT_STATUS_NOT_ALLOWED,
      'title'   => t('Operation not allowed for the card or terminal'),
      'description'   => t('Operation not allowed for the card or terminal'),
    ]),
    new PaymentStatusInfo([
      'parent'  => PAYMENT_STATUS_FAILED,
      'status'  => SERMEPA_PAYMENT_STATUS_INSUFFICIENT,
      'title'   => t('Asset insufficient'),
      'description'   => t('Asset insufficient'),
    ]),
    new PaymentStatusInfo([
      'parent'  => PAYMENT_STATUS_FAILED,
      'status'  => SERMEPA_PAYMENT_STATUS_UNREGISTERED,
      'title'   => t('Card not registered'),
      'description'   => t('Card not registered'),
    ]),
    new PaymentStatusInfo([
      'parent'  => PAYMENT_STATUS_FAILED,
      'status'  => SERMEPA_PAYMENT_STATUS_SECURITY_CODE,
      'title'   => t('Wrong security code (CVV2/CVC2)'),
      'description'   => t('Wrong security code (CVV2/CVC2)'),
    ]),
    new PaymentStatusInfo([
      'parent'  => PAYMENT_STATUS_FAILED,
      'status'  => SERMEPA_PAYMENT_STATUS_OUT_OF_SERVICE,
      'title'   => t('Card out of service'),
      'description'   => t('Card out of service'),
    ]),
    new PaymentStatusInfo([
      'parent'  => PAYMENT_STATUS_FAILED,
      'status'  => SERMEPA_PAYMENT_STATUS_OWNER_AUTHENTICATION,
      'title'   => t('Error on owner authentication'),
      'description'   => t('Error on owner authentication'),
    ]),
    new PaymentStatusInfo([
      'parent'  => PAYMENT_STATUS_FAILED,
      'status'  => SERMEPA_PAYMENT_STATUS_NO_REASONS,
      'title'   => t('Denied without specific reasons'),
      'description'   => t('Denied without specific reasons'),
    ]),
    new PaymentStatusInfo([
      'parent'  => PAYMENT_STATUS_FAILED,
      'status'  => SERMEPA_PAYMENT_STATUS_WRONG_EXPIRATION,
      'title'   => t('Wrong expiration date'),
      'description'   => t('Wrong expiration date'),
    ]),
    new PaymentStatusInfo([
      'parent'  => PAYMENT_STATUS_FAILED,
      'status'  => SERMEPA_PAYMENT_STATUS_FRAUD,
      'title'   => t('Temporary or emergency card on suspicion of withdrawal card fraud'),
      'description'   => t('Temporary or emergency card on suspicion of withdrawal card fraud'),
    ]),
    new PaymentStatusInfo([
      'parent'  => PAYMENT_STATUS_FAILED,
      'status'  => SERMEPA_PAYMENT_STATUS_ISSUER_UNAVAILABLE,
      'title'   => t('Issuer not available'),
      'description'   => t('Issuer not available'),
    ]),
    new PaymentStatusInfo([
      'parent'  => PAYMENT_STATUS_FAILED,
      'status'  => SERMEPA_PAYMENT_STATUS_ORDER_DUPLICATED,
      'title'   => t('Order duplicated'),
      'description'   => t('Order duplicated'),
    ]),
    new PaymentStatusInfo([
      'parent'  => PAYMENT_STATUS_FAILED,
      'status'  => SERMEPA_PAYMENT_STATUS_TRANSACTION_REFUSED,
      'title'   => t('Transaction refused'),
      'description'   => t('Transaction refused'),
    ]),
  ];
}
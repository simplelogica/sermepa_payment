<?php

/**
 * Contains \Drupal\sermepa_payment\Plugin\Payment\Method\SermepaOperationsProvider.
 */

namespace Drupal\sermepa_payment\Plugin\Payment\Method;

use Drupal\payment\Plugin\Payment\Method\BasicOperationsProvider;

/**
 * Abstract class for Sermepa payment method operation providers.
 */
class SermepaOperationsProvider extends BasicOperationsProvider {

  /**
   * {@inheritdoc}
   */
  protected function getPaymentMethodConfiguration($plugin_id) {
    $entity_id = explode(':', $plugin_id)[1];

    return $this->paymentMethodConfigurationStorage->load($entity_id);
  }

}

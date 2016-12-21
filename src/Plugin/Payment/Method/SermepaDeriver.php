<?php

/**
 * Contains \Drupal\sermepa_payment\Plugin\Payment\Method\SermepacDeriver.
 */

namespace Drupal\sermepa_payment\Plugin\Payment\Method;

use Drupal\payment\Plugin\Payment\Method\BasicDeriver;

/**
 * Abstract class for Sermepa payment method derivers.
 */
class SermepaDeriver extends BasicDeriver {

  /**
   * @inheritDoc
   */
  protected function getId() {
    return 'sermepa_payment';
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    /** @var \Drupal\payment\Entity\PaymentMethodConfigurationInterface[] $payment_methods */
    $payment_methods = $this->paymentMethodConfigurationStorage->loadMultiple();
    foreach ($payment_methods as $payment_method) {
      if ($payment_method->getPluginId() == $this->getId()) {
        /** @var \Drupal\paypal_payment\Plugin\Payment\MethodConfiguration\PayPalBasic $configuration_plugin */
        $configuration_plugin = $this->paymentMethodConfigurationManager->createInstance($payment_method->getPluginId(), $payment_method->getPluginConfiguration());
        $this->derivatives[$payment_method->id()] = [
          'id' => $base_plugin_definition['id'] . ':' . $payment_method->id(),
          'active' => $payment_method->status(),
          'label' => $configuration_plugin->getBrandLabel() ? $configuration_plugin->getBrandLabel() : $payment_method->label(),
          'message_text' => $configuration_plugin->getMessageText(),
          'message_text_format' => $configuration_plugin->getMessageTextFormat(),
          'execute_status_id' => $configuration_plugin->getExecuteStatusId(),
          'capture' => $configuration_plugin->getCapture(),
          'capture_status_id' => $configuration_plugin->getCaptureStatusId(),
          'refund' => $configuration_plugin->getRefund(),
          'refund_status_id' => $configuration_plugin->getRefundStatusId(),
        ] + $configuration_plugin->getDerivativeConfiguration() + $base_plugin_definition;
      }
    }

    return $this->derivatives;
  }

}

<?php


/**
 * Creates and validates the settings form for the Sermepa Gateway.
 */
class SermepaSettingsForm {

  /**
   * Creates the settings form for Sermepa Payment Methods.
   */
  function create($values) {
    return array(
      'currency' => array(
        '#default_value' => $values['currency'],
        '#required' => true,
        '#title' => t('Currency code'),
        '#type' => 'select',
        '#options' => Sermepa::getAvailableCurrencies(),
      ),
      'titular' => array(
        '#default_value' => $values['titular'],
        '#required' => false,
        '#title' => t('Name and surname of the account owner'),
        '#type' => 'textfield',
      ),
      'merchant_name' => array(
        '#default_value' => $values['merchant_name'],
        '#required' => false,
        '#title' => t('Commerce name'),
        '#type' => 'textfield',
      ),
      'merchant_code' => array(
        '#default_value' => $values['merchant_code'],
        '#required' => true,
        '#title' => t('Commerce FUC code'),
        '#type' => 'textfield',
      ),
      'terminal' => array(
        '#default_value' => $values['terminal'],
        '#required' => true,
        '#title' => t('Terminal number'),
        '#type' => 'textfield',
      ),
      'signature' => array(
        '#default_value' => $values['signature'],
        '#required' => true,
        '#title' => t('Secret encryption key'),
        '#type' => 'textfield',
      ),
      'environment' => array(
        '#default_value' => $values['environment'],
        '#required' => true,
        '#title' => t('Environment'),
        '#type' => 'select',
        '#options' => Sermepa::getAvailableEnvironments(),
      ),
      'encryption_method' => array(
        '#default_value' => $values['encryption_method'],
        '#required' => true,
        '#title' => t('Secret encryption key'),
        '#type' => 'select',
        '#options' => Sermepa::getAvailableEncryptionMethods(),
      ),
    );
  }


  /**
   * Validates and saves the settings of the Sermepa Payment Methods.
   */
  function validate($method, $values) {
    $method->controller_data = array(
      'currency'          => $values['currency'],
      'titular'           => $values['titular'],
      'merchant_name'     => $values['merchant_name'],
      'merchant_code'     => $values['merchant_code'],
      'terminal'          => $values['terminal'],
      'signature'         => $values['signature'],
      'environment'       => $values['environment'],
      'encryption_method' => $values['encryption_method'],
    );
  }

}
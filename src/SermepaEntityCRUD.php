<?php


class SermepaEntityCRUD {

  private static $table = 'sermepa_payment_payment_method';


  static function load(array $entities) {
    $pmids = self::getPmids($entities);
    if (!empty($pmids)) {
      $query = db_select(self::$table)
        ->fields(self::$table)
        ->condition('pmid', $pmids);
      $result = $query->execute();
      while ($data = $result->fetchAssoc()) {
        $payment_method = $entities[$data['pmid']];
        $payment_method->controller_data = (array) $data;
        unset($payment_method->controller_data['pmid']);
      }
    }
  }


  static function insert(PaymentMethod $method) {
    $values = self::getConfigValues($method);
    drupal_write_record(self::$table, $values);
  }


  static function update(PaymentMethod $method) {
    $values = self::getConfigValues($method);
    drupal_write_record(self::$table, $values, array('pmid'));
  }


  static function delete(PaymentMethod $method) {
    db_delete(self::$table)
      ->condition('pmid', $method->pmid)
      ->execute();
  }


  private static function getPmids($entities) {
    $sermepa_methods = array_filter($entities, function ($method) {
      return $method->controller->name === 'SermepaPaymentMethodController';
    });
    return array_map(function ($method) {
      return $method->pmid;
    }, $sermepa_methods);
  }


  static private function getConfigValues(PaymentMethod $method) {
    $new_values = $method->controller_data;
    $default_values = $method->controller->controller_data_defaults;
    $values = array_merge($default_values, $new_values);
    $values['pmid'] = $method->pmid;
    return $values;
  }
}
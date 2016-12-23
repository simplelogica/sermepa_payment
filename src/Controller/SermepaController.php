<?php

namespace Drupal\sermepa_payment\Controller;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;

class SermepaController extends ControllerBase {

  /**
   * Checks access for a specific request.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   */
  public function access(AccountInterface $account) {
    return AccessResult::allowedIf(true);
  }

  /**
    * Action process Sermepa response
    *
    * @param Request $request
    *   Request data, including the step variable tat indicates current step.
    */
  public function callback(Request $request) {
    // Do something magic.
    return [];
  }
}

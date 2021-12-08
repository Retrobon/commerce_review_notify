<?php

namespace Drupal\commerce_review_notify;

use Drupal\Core\Entity\ContentEntityInterface;

interface CommerceReviewNotifyInterface extends ContentEntityInterface {

  /**
   *
   * @return string
   *
   */
  public function getOrderId();

  /**
   *
   * @param string $orderId
   *
   * @return CommerceReviewNotifyInterface
   */
  public function setOrderId($orderId);

}

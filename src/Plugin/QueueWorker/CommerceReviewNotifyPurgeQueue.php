<?php

namespace Drupal\commerce_review_notify\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;

/**
 *
 * @QueueWorker(
 *   id = "commerce_review_notify_cleanup",
 *   title = @Translation("Commerce Review Notification Purge Queue"),
 *   cron = {"time" = 240}
 * )
 */
class CommerceReviewNotifyPurgeQueue extends QueueWorkerBase {

  /**
   * {@inheritdoc}
   */
  public function processItem($data) {
    $data->delete();
  }

}

<?php

namespace Drupal\commerce_review_notify;

use Drupal\commerce_order\Entity\Order;
use Drupal\commerce_review_notify\Form\AdminConfigForm;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

/**
 * Provides a list controller for the commerce review notify entity type.
 */
class CommerceReviewNotifyListBuilder extends EntityListBuilder
{

  /**
   * {@inheritdoc}
   */
  public function buildHeader()
  {
    $header['email'] = $this->t('User E-Mail');
    $header['order'] = $this->t('Заказ №');
    $header['submit'] = $this->t('Заказ оформлен');
    $header['sent'] = $this->t('Письмо отправлено');

    return $header;
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity)
  {
    $order = Order::load($entity->getOrderId());
    /* @var $entity \Drupal\commerce_review_notify\Entity\CommerceReviewNotify */

    $submit = date(DateTimeItemInterface::DATETIME_STORAGE_FORMAT, $entity->get('submit_time')->getString());
    $row['email'] = $order->getEmail();
    $row['order'] = $order->toLink();
    $row['submit'] = \Drupal::service('date.formatter')->format($entity->get('submit_time')->getString(), 'custom', 'd/m/Y H:i');
    if (!empty($entity->get('sent_time')->getString())) {
      $row['sent'] = 'Было отправлено: ' . \Drupal::service('date.formatter')->format($entity->get('sent_time')
          ->getString(), 'custom', 'd/m/Y H:i');
    } else {
      $sending_interval = AdminConfigForm::getValuesFromConfig()['sending_interval'];
      $sending_time = strtotime($submit . ' +' . $sending_interval . 'days');
      $row['sent'] = 'Будет отправлено: ' . \Drupal::service('date.formatter')->format($sending_time, 'custom', 'd/m/Y H:i');
    }
    return $row;
  }

}

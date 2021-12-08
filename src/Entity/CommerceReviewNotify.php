<?php

namespace Drupal\commerce_review_notify\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\commerce_review_notify\CommerceReviewNotifyInterface;

/**
 * Defines the commerce review notify entity class.
 *
 * @ContentEntityType(
 *   id = "commerce_review_notify",
 *   label = @Translation("commerce review notify"),
 *   label_collection = @Translation("commerce review notifies"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\commerce_review_notify\CommerceReviewNotifyListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\commerce_review_notify\CommerceReviewNotifyAccessControlHandler",
 *     "form" = {
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\commerce_review_notify\CommerceReviewNotifyHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "commerce_review_notify",
 *   admin_permission = "access commerce review notify entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "order_id",
 *     "uuid" = "uuid",
 *     "status" = "status",
 *   },
 *   links = {
 *     "delete-form" = "/admin/structure/commerce_review_notify/{commerce_review_notify}/delete",
 *     "collection" = "/admin/structure/commerce_review_notify",
 *     "delete-multiple-form" = "/admin/structure/commerce_review_notify/delete"
 *   },
 *   field_ui_base_route = "commerce_review_notify.admin_config_form"
 * )
 */
class CommerceReviewNotify extends ContentEntityBase implements CommerceReviewNotifyInterface
{


  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type)
  {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['order_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Order'))
      ->setDescription(t('The order that was completed.'))
      ->setSetting('target_type', 'commerce_order')
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -1,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['submit_time'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Submitted on'))
      ->setDescription(t('The time that the notification request was submitted.'))
      ->setDisplayOptions('view', [
        'type' => 'timestamp',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['sent_time'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Sent on'))
      ->setDescription(t('The time that the notification was sent to the user.'))
      ->setSettings([
        'datetime_type' => 'date',
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'datetime_default',
        'settings' => [
          'format_type' => 'medium',
        ],
        'weight' => -9,
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => -9,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getOrderId()
  {
    return $this->get('order_id')->getString();
  }

  /**
   * {@inheritdoc}
   */
  public function setOrderId($orderId)
  {
    $this->set('order_id', $orderId);
    return $this;
  }
}

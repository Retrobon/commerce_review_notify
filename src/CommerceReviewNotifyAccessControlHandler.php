<?php

namespace Drupal\commerce_review_notify;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the commerce review notify entity type.
 */
class CommerceReviewNotifyAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {

    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view commerce review notify');

      case 'update':
        return AccessResult::allowedIfHasPermissions($account, ['edit commerce review notify', 'administer commerce review notify'], 'OR');

      case 'delete':
        return AccessResult::allowedIfHasPermissions($account, ['delete commerce review notify', 'administer commerce review notify'], 'OR');

      default:
        // No opinion.
        return AccessResult::neutral();
    }

  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermissions($account, ['create commerce review notify', 'administer commerce review notify'], 'OR');
  }

}

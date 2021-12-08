<?php

namespace Drupal\commerce_review_notify\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\state_machine\Event\WorkflowTransitionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderCompleteSubscriber implements EventSubscriberInterface
{

  /**
   * @var EntityTypeManagerInterface
   */
  private EntityTypeManagerInterface $entityTypeManager;

  /**
   * @param EntityTypeManagerInterface $entityTypeManager
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager)
  {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents()
  {
    $events = [
      'commerce_order.precompleted.post_transition' => ['createNotify', -100],
    ];
    return $events;
  }

  /**
   * @param WorkflowTransitionEvent $event
   * @return void
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function createNotify(WorkflowTransitionEvent $event)
  {
    $order = $event->getEntity();
    $notifyStorage = $this->entityTypeManager->getStorage('commerce_review_notify');
    $notify = $notifyStorage->create([
      'order_id' => $order->id(),
    ]);
    $notify->save();
  }

}

<?php

namespace Drupal\commerce_review_notify\Plugin\QueueWorker;

use Drupal\commerce_order\Entity\Order;
use Drupal\commerce_review_notify\Form\AdminConfigForm;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\Core\Utility\Token;
use Drupal\mail_notify\MailHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Sends out notifications when items are in stock.
 *
 * @QueueWorker(
 *   id = "commerce_review_notify",
 *   title = @Translation("Commerce Review Notify Queue"),
 *   cron = {"time" = 120}
 * )
 */
class CommerceReviewNotifyQueue extends QueueWorkerBase implements ContainerFactoryPluginInterface
{

  use StringTranslationTrait;

  /**
   * Token.
   *
   * @var \Drupal\Core\Utility\Token
   */
  private $token;

  /**
   * @param array $configuration
   * @param $plugin_id
   * @param $plugin_definition
   * @param Token $token
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Token $token) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->token = $token;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('token')
    );
  }
  /**
   * {@inheritdoc}
   */
  public function processItem($data)
  {
    if (empty($data->get('sent_time')->getValue())) {
      /** @var MailHandler $mailHandler */
      $mailHandler = \Drupal::service('mail_notify.mail_handler');
      $order = Order::load($data->getOrderId());
      $subject = $this->token->replace(AdminConfigForm::getValuesFromConfig()['email_subject']);
      $shipping_profile = $order->get('shipments')[0]->entity->shipping_profile[0]->entity;
      $name = $shipping_profile->get('address')[0]->given_name . ' ' . $shipping_profile->get('address')[0]->family_name;
      $comment_links = [];
      foreach ($order->getItems() as $item) {
        $url = Url::fromRoute('comment.reply', ['entity_type' => 'commerce_product', 'field_name' => 'field_comments', 'entity' => $item->getPurchasedEntity()->getProductId()], ['absolute' => 'true']);
        $comment_link = Link::fromTextAndUrl(t('Оставить отзыв ') . $item->getTitle(), $url);
        $comment_links[] = $comment_link->toRenderable();
      }
      $body = [
        '#theme' => 'mail_review_notify',
        '#order_entity' => $order,
        '#name' => $name,
        '#comment_links' => $comment_links
      ];
      \Drupal::logger('commerce_review_notify')->notice('send review notify mail for order #' . $order->id());
      $mailHandler->sendMail($order->getEmail(), $subject, $body);
      $data->set('sent_time', time())->save();
    }
  }

}

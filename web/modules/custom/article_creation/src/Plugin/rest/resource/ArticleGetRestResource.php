<?php

namespace Drupal\article_creation\Plugin\rest\resource;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Provides a resource to get Articles bundles.
 *
 * @RestResource(
 *   id = "article_get_rest_resource",
 *   label = @Translation("Article Get Rest Resource"),
 *   uri_paths = {
 *     "canonical" = "/article-rest"
 *   }
 * )
 */
class ArticleGetRestResource extends ResourceBase {
  /**
   * A current user instance which is logged in the session.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   *   AccountProxyInterface.
   */
  protected AccountProxyInterface $currentUser;

  /**
   * EntityTypeManager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   *   EntityTypeManagerInterface.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * Constructs a Drupal\rest\Plugin\ResourceBase object.
   *
   * @param array $config
   *   A configuration array which contains the information about the plugin instance.
   * @param string $module_id
   *   The module_id for the plugin instance.
   * @param mixed $module_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   A currently logged user instance.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity Type manager.
   */
  public function __construct(
    array $config,
          $module_id,
          $module_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    AccountProxyInterface $current_user,
    EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($config, $module_id, $module_definition, $serializer_formats, $logger);

    $this->currentUser = $current_user;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $config, $module_id, $module_definition) {
    return new static(
      $config,
      $module_id,
      $module_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('article_rest_resource'),
      $container->get('current_user'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * Returns a list of articles.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *
   * @return \Drupal\rest\ResourceResponse
   *   Response from rest.
   */
  public function get() {
    // You must to implement the logic of your REST Resource here.
    // Use current user after pass authentication to validate access.
    if (!$this->currentUser->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }
    $storage = $this->entityTypeManager->getStorage('node');
    $nids = $storage->getQuery()
      ->condition('status', 1)
      ->condition('type', 'article')
      ->execute();
    $nodes = $storage->loadMultiple($nids);
    $result = [];
    foreach ($nodes as $node) {
      $url = $node->toUrl()->toString(TRUE);
      $result[] = [
        'nid' => $node->id(),
        'bundle' => $node->bundle(),
        'path' => $url->getGeneratedUrl(),
        'title' => $node->getTitle(),
        'description' => $node->getDescription(),
        'image' => $node->getImage(),
      ];
    }

    $response = new ResourceResponse($result);
    $response->addCacheableDependency($result);
    return $response;
  }

}

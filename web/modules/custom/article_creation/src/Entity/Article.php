<?php

namespace Drupal\article_creation\Entity;

use Drupal\node\Entity\Node;

/**
 * Article Bundle.
 */
class Article extends Node implements NodeArticleInterface {

  /**
   * {@inheritDoc}
   */
  public function getTitle(): string {
    return parent::getTitle();
  }

  /**
   * {@inheritDoc}
   */
  public function getDescription(): string {
    return $this->get('body')->value;
  }

  /**
   * {@inheritDoc}
   */
  public function getImage(): string {
    $image_uri = $this->get('field_image')->entity->getFileUri();
    $image_url = \Drupal::service('file_url_generator')->generateAbsoluteString($image_uri);
    return $image_url;
  }

}

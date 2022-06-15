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
  public function getDescription(): ?string {
    return !$this->get('body')->isEmpty() ? $this->get('body')->value: '';
  }

  /**
   * {@inheritDoc}
   */
  public function getImage(): ?string {
    if (!$this->get('field_image')->isEmpty()) {
      $image_uri = $this->get('field_image')->entity->getFileUri();
      return \Drupal::service('file_url_generator')->generateAbsoluteString($image_uri);
    }
    return '';

  }

}

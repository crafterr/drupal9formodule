<?php

namespace Drupal\article_creation\Entity;

/**
 * NodeArticleInterface.
 */
interface NodeArticleInterface {

  /**
   * Get Article.
   *
   * @return string
   *   Return Title from node.
   */
  public function getTitle(): string;

  /**
   * Get Description.
   *
   * @return string
   *   Return Description form node.
   */
  public function getDescription(): ?string;

  /**
   * Get Image.
   *
   * @return string
   *   Return image url from node.
   */
  public function getImage(): ?string;

}

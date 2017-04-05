<?php

/**
 * @file
 * Contains \Drupal\taxonomy_rating\TaxonomyRatingService.
 */

namespace Drupal\taxonomy_rating;


use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManager;

class TaxonomyRatingService {

  protected $connection;
  protected $entity_manager;


  /**
   * Set global value for storage connection and entitymanager.
   */
  public function __construct(Connection $connection, EntityTypeManager $entity_manager) {
    $this->connection = $connection;
    $this->entity_manager = $entity_manager;
  }

  /**
   * Write calculated data to taxonomy.
   */
  public function calculateTaxonomyRating($tid) {
    // Get Node counts.
    $query = $this->connection->select('taxonomy_index', 'ti');
    $query->fields('ti', ['tid', 'nid']);
    $query->condition('ti.tid', $tid);
    $node_count_result = $query->countQuery()->execute();
    $node_count_value = $node_count_result->fetchField();

    // Get corresponding comments for attached Nodes.
    $query = $this->connection->select('taxonomy_index', 'ti');
    $query->fields('ti', ['tid', 'nid']);
    $query->condition('ti.tid', $tid);
    $query->leftJoin('comment_field_data', 'cfd', 'ti.nid = cfd.entity_id');
    $comment_count_result = $query->countQuery()->execute();
    $comment_count_value = $comment_count_result->fetchField();

    $rating = $node_count_value * 5 + $comment_count_value * 0.1;
    $entity_manager = $this->entity_manager;
    $term = $entity_manager->getStorage('taxonomy_term')->load($tid);
    $term->field_rating->setValue($rating);
    $term->save();
  }
}

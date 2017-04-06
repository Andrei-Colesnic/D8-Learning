<?php

/**
 * @file
 * Contains \Drupal\taxonomy_rating\TaxonomyRatingService.
 */

namespace Drupal\taxonomy_rating;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManager;

class TaxonomyRatingService {

  protected $connection;
  protected $entity_manager;
  protected $config_factory;


  /**
   * Set global value for storage connection, entity manager and config factory.
   */
  public function __construct(Connection $connection, EntityTypeManager $entity_manager, ConfigFactory $config_factory) {
    $this->connection = $connection;
    $this->entityManager = $entity_manager;
    $this->configFactory = $config_factory;
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

    $node_multiplier = $this->config_factory->get('taxonomy_rating.multiplier')->get('node_multiplier');
    $comment_multiplier = $this->config_factory->get('taxonomy_rating.multiplier')->get('comment_multiplier');
    $rating = $node_count_value * $node_multiplier + $comment_count_value * $comment_multiplier;
    $term = $this->entity_manager->getStorage('taxonomy_term')->load($tid);
    $term->field_rating->setValue($rating);
    $term->save();
  }
}

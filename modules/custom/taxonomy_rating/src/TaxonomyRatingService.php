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
   * Write Node rating data to storage.
   */
  public function writeNodeRating($tid, $nid) {
    try {
      $query = $this->connection->upsert('taxonomy_node_rating');
      $query->fields(['tid', 'nid',]);
      $query->values([$tid, $nid,]);
      $query->key('nid');
      $query->execute();

      if(!$query) {
        throw new \Exception('Insert unsuccessful');
      }
    }
    catch (\Exception $e) {
      watchdog_exception('taxonomy_node_rating', $e);
    }
  }

  /**
   * Write Comment rating data to storage.
   */
  public function writeCommentRating($tid, $cid) {
    try {
      $query = $this->connection->upsert('taxonomy_comment_rating');
      $query->fields(['tid', 'cid',]);
      $query->values([$tid, $cid,]);
      $query->key('cid');
      $query->execute();

      if(!$query) {
        throw new \Exception('Insert unsuccessful');
      }
    }
    catch (\Exception $e) {
      watchdog_exception('taxonomy_comment_rating', $e);
    }
  }

  /**
   * Write calculated data to taxonomy.
   */
  public function writeRatingToTaxonomy($tid) {
    // Get all views for node.
    $query = $this->connection->select('taxonomy_node_rating', 'tnr');
    $query->fields('tnr', ['tid', 'nid']);
    $query->condition('tnr.tid', $tid);
    $node_count_result = $query->countQuery()->execute();
    $node_count_value = $node_count_result->fetchField();

    $query = $this->connection->select('taxonomy_comment_rating', 'tcr');
    $query->fields('tcr', ['tid', 'cid']);
    $query->condition('tcr.tid', $tid);
    $comment_count_result = $query->countQuery()->execute();
    $comment_count_value = $comment_count_result->fetchField();

    $rating = $node_count_value * 5 + $comment_count_value *0.1;
    $entity_manager = $this->entity_manager;
    $term = $entity_manager->getStorage('taxonomy_term')->load($tid);
    $term->field_rating->setValue($rating);
    $term->save();
  }

  /**
   * Get Node Entity from comment.
   */
  public function getNodeFromComment($entity) {
    $nid = $entity->get('entity_id')->target_id;
    $entity_manager = $this->entity_manager;
    return $entity_manager->getStorage('node')->load($nid);
  }

  /**
   * Get Tid from Node.
   */
  public function getTidFromNode($entity) {
    $tid = 0;
    if ($tid_from_node = $entity->get('field_article_type')->getValue()) {
      $tid = $tid_from_node[0]['target_id'];
    }
    return $tid;
  }

}

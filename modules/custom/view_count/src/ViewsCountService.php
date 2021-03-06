<?php

/**
 * @file
 * Contains \Drupal\view_count\ViewsCountService.
 */

namespace Drupal\view_count;


use Drupal\Core\Database\Connection;

class ViewsCountService {

  protected $connection;


  /**
   * Set global value for storage connection.
   */
  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  /**
   * Return the Today views data for the Node View.
   */
  public function getTodayViewData($nid) {
    // Get page views for last 24 hours.
    $yesterday = strtotime('-1 day');
    $query = $this->connection->select('view_count', 'vc');
    $query->fields('vc', ['uid', 'timestamp']);
    $query->condition('vc.nid', $nid);
    $query->condition('vc.timestamp', $yesterday, '>');
    $query_result = $query->countQuery()->execute();
    return $query_result->fetchField();
  }

  /**
   * Return the Last views data for the Node View.
   */
  public function getLastViewData($nid) {
    // Get all page views.
    $query = $this->connection->select('view_count', 'vc');
    $query->fields('vc', ['uid', 'timestamp']);
    $query->condition('vc.nid', $nid);
    $query->orderBy('timestamp', 'DESC');
    $query->range(0, 1);
    return $query->execute()->fetchAssoc();
  }

  /**
   * Return the All Views data for the Node View.
   */
  public function getAllViewData($nid) {
    // Get all views for node.
    $query = $this->connection->select('view_count', 'vc');
    $query->fields('vc', ['uid', 'timestamp']);
    $query->condition('vc.nid', $nid);
    $query_result = $query->countQuery()->execute();
    return $query_result->fetchField();
  }

  /**
   * Write current view to db.
   */
  public function setViewData($nid, $uid) {
    try {
      $timestamp = time();
      $query = $this->connection->insert('view_count')
        ->fields(array('nid', 'timestamp', 'uid'))
        ->values([$nid, $timestamp, $uid])
        ->execute();

      if(!$query) {
        throw new \Exception('Insert unsuccessful');
      }
    }
    catch (\Exception $e) {
      watchdog_exception('view_count', $e);
    }
  }
}

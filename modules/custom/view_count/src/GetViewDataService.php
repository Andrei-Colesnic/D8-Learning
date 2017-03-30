<?php

/**
 * @file
 * Contains \Drupal\view_count\GetViewDataService.
 */

namespace Drupal\view_count;


use Drupal\Core\Database\Connection;

class GetViewDataService {

  protected $all_views;
  protected $page_view;
  protected $connection;


  /**
   * When the service is created, set a value for the example variable.
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
    $today_views = $query_result->fetchField();

    return $today_views;
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
    $page_view = $query->execute()->fetchAssoc();

    return $page_view;
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
    $all_views = $query_result->fetchField();

    return $all_views;
  }

  /**
   * Write current view to db.
   */
  public function setViewData($nid, $uid) {
    $timestamp = time();
    $query = $this->connection->insert('view_count')
      ->fields(array('nid', 'timestamp', 'uid'))
      ->values([$nid, $timestamp, $uid])
      ->execute();
  }
}

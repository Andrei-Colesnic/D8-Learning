<?php

/**
 * @file
 * Contains \Drupal\view_count\GetViewDataService.
 */

namespace Drupal\view_count;

class GetViewDataService {

  protected $all_views;
  protected $today_views;
  protected $page_view;


  /**
   * When the service is created, set a value for the example variable.
   */
  public function __construct() {

  }

  /**
   * Return the Today views data for the Node View.
   */
  public function getTodayViewData($nid) {
    // Get page views for last 24 hours.
    $yesterday = strtotime('-1 day');
    $query = \Drupal::database()->select('view_count', 'vc');
    $query->fields('vc', ['uid', 'timestamp']);
    $query->condition('vc.nid', $nid);
    $query->condition('vc.timestamp', $yesterday, '>');
    $query_result = $query->countQuery()->execute();
    $this->today_views = $query_result->fetchField();

    return $this->today_views;
  }

  /**
   * Return the Last views data for the Node View.
   */
  public function getLastViewData($nid) {
    // Get all page views.
    $query = \Drupal::database()->select('view_count', 'vc');
    $query->fields('vc', ['uid', 'timestamp']);
    $query->condition('vc.nid', $nid);
    $query->orderBy('timestamp', 'DESC');
    $query->range(0, 1);
    $this->page_view = $query->execute()->fetchAssoc();

    return $this->page_view;
  }

  /**
   * Return the All Views data for the Node View.
   */
  public function getAllViewData($nid) {
    // Get all views for node.
    $query = \Drupal::database()->select('view_count', 'vc');
    $query->fields('vc', ['uid', 'timestamp']);
    $query->condition('vc.nid', $nid);
    $query_result = $query->countQuery()->execute();
    $this->all_views = $query_result->fetchField();

    return $this->all_views;
  }

  /**
   * Write current view to db.
   */
  public function setViewData($nid) {
    // Insert data into db, if not ano nymous user.
    if (!\Drupal::currentUser()->isAnonymous()) {
      $timestamp = time();
      $uid = \Drupal::currentUser()->id();
      $query = \Drupal::database()->insert('view_count')
        ->fields(array('nid', 'timestamp', 'uid'))
        ->values([$nid, $timestamp, $uid])
        ->execute();
    }
  }
}

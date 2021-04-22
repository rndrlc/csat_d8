<?php

namespace Drupal\csat\Form;

use Drupal\Core\Database\Database;

/**
 * CRUD functions for Csat.
 */
class CsatClass {

  /**
   * Get all content feedbacks.
   *
   * @param array $header
   *   The table header.
   * @param string $type
   *   The type of the overview form ('open' or 'resolved').
   *
   * @return array
   *   Return associative array of all feedbacks.
   */
  public static function getAllCsat(array $header, $type) {
    $connection = Database::getConnection();
    $fields     = [
      'pid', 
      'comment', 
      'rating', 
      'score', 
      'created_at'
    ];
    $list       = $connection->select('csat', 'cf');
    $list->fields('cf', $fields);
    // $list->condition('status', $status);
    $table_sort = $list->extend('Drupal\Core\Database\Query\TableSortExtender')->orderByHeader($header);
    // $pager      = $table_sort->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit(50);
    $results    = $table_sort->execute()->fetchAll();

    return $results;
  }

}

<?php
/**
 * NotifyStock for Commerce.
 *
 * Copyright 2020 by Tony Klapatch <tony@klapatch.net>
 *
 * This file is meant to be used with Commerce by modmore. A valid Commerce license is required.
 *
 * @package commerce_notifystock
 * @license See core/components/commerce_notifystock/docs/license.txt
 */

$xpdo_meta_map['NotifyStockRequest']= array (
  'package' => 'commerce_notifystock',
  'version' => '1.1',
  'extends' => 'comSimpleObject',
  'table' => 'commerce_notify_stock_request',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'user' => 0,
    'email' => NULL,
    'product' => NULL,
    'conditions' => '',
    'message' => NULL,
    'added_on' => NULL,
    'completed' => 0,
    'completed_on' => NULL,
    'removed' => 0,
    'removed_on' => NULL,
    'removed_by' => 0,
  ),
  'fieldMeta' => 
  array (
    'user' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'int',
      'null' => false,
      'default' => 0,
    ),
    'email' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '320',
      'phptype' => 'string',
      'null' => false,
    ),
    'product' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'int',
      'null' => false,
    ),
    'conditions' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'array',
      'null' => true,
      'default' => '',
    ),
    'message' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'int',
      'null' => false,
    ),
    'added_on' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => false,
    ),
    'completed' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 0,
    ),
    'completed_on' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => false,
    ),
    'removed' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 0,
    ),
    'removed_on' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => true,
    ),
    'removed_by' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 0,
    ),
  ),
  'indexes' => 
  array (
    'removed' => 
    array (
      'alias' => 'removed',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'removed' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'User' => 
    array (
      'class' => 'modUser',
      'local' => 'user',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Product' => 
    array (
      'class' => 'comProduct',
      'local' => 'product',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Message' => 
    array (
      'class' => 'NotifyStockMessage',
      'local' => 'message',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);

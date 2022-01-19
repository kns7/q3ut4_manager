<?php
$serviceContainer = \Propel\Runtime\Propel::getServiceContainer();
$serviceContainer->initDatabaseMaps(array (
  'default' => 
  array (
    0 => '\\Map\\ConfigTableMap',
    1 => '\\Map\\GametypesTableMap',
    2 => '\\Map\\MapsTableMap',
  ),
));

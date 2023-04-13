<?php

if (!defined('ABSPATH')) {
    exit();
}
$action = isset($_GET['action']) && in_array($_GET['action'], array('add', 'edit', 'ordering')) ? $_GET['action'] : 'ordering';
$type = isset($_GET['type']) && in_array($_GET['type'], array('html', 'builder', 'default')) ? $_GET['type'] : 'builder';
$key = isset($_GET['key']) && ($s = trim($_GET['key'])) ? $s : '';
if ($action === 'edit' && !$key) {
    $action = 'ordering';
}
include_once 'layout-member_tabs/' . $action . '.php';

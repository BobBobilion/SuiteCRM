<?php
/**
 * SuiteCRM Trade-In Manager - Dashlet Meta Configuration
 * 
 * This file defines the dashlet metadata and configuration options.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

global $dashletMeta;

$dashletMeta['DM_TradeInsDashlet'] = array(
    'module' => 'DM_TradeIns',
    'title' => translate('LBL_DASHLET_MY_TRADES', 'DM_TradeIns'),
    'description' => translate('LBL_DASHLET_RECENT_TRADES', 'DM_TradeIns'),
    'icon' => 'icon-car',
    'category' => 'Module Views',
);
?> 
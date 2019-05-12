<?php
/**
 * Таблица БД
 */
//$config['$root$']['db']['table']['like_like_target'] = '___db.table.prefix___like_target';
//$config['$root$']['db']['table']['like_like'] = '___db.table.prefix___like';

$config['$root$']['router']['page']['media'] = 'PluginMedia_ActionMedia';

$config['admin']['assets'] = [
    'js' => [
        'assets/js/init.js'
    ],
    'css' => [
        //'assets/css/admin.css'
    ]
]; 


return $config;
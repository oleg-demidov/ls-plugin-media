<?php
/**
 * Таблица БД
 */
$config['$root$']['db']['table']['media_media'] = '___db.table.prefix___media';
$config['$root$']['db']['table']['media_media_target'] = '___db.table.prefix___media_target';

$config['$root$']['router']['page']['media'] = 'PluginMedia_ActionMedia';

$config['components'] = [
    'media:library', 'media:tinymce-plugin', 'bootstrap', 'bs-icon'
];

$config['admin']['assets'] = [
    'js' => [
        'assets/js/init.js'
    ],
    'css' => [
        //'assets/css/admin.css'
    ]
]; 
/*
 * Добавление компонентов в админке
 */
$config['$root$']['plugin']['admin']['components'] = array_merge(
    Config::Get('plugin.admin.components'), 
    $config['components']
);

$config['library']['per_page'] = 20;


// Модуль Image
$config['$root$']['module']['image']['driver'] = 'gd';
$config['$root$']['module']['image']['params']['default']['size_max_width'] = 7000;
$config['$root$']['module']['image']['params']['default']['size_max_height'] = 7000;
$config['$root$']['module']['image']['params']['default']['format_auto'] = true;
$config['$root$']['module']['image']['params']['default']['format'] = 'jpg';
$config['$root$']['module']['image']['params']['default']['quality'] = 95;
$config['$root$']['module']['image']['params']['default']['watermark_use'] = false;    // Использовать ватермарк или нет
$config['$root$']['module']['image']['params']['default']['watermark_type'] = 'image'; // Тип: image - накладывается изображение. Другие типы пока не поддерживаются
$config['$root$']['module']['image']['params']['default']['watermark_image'] = null; // Полный серверный путь до картинки ватермарка
$config['$root$']['module']['image']['params']['default']['watermark_position'] = 'bottom-right'; // Значения: bottom-left, bottom-right, top-left, top-right, center
$config['$root$']['module']['image']['params']['default']['watermark_min_width'] = 100; // Минимальная ширина изображения, начиная с которой будет наложен ватермарк
$config['$root$']['module']['image']['params']['default']['watermark_min_height'] = 100; // Минимальная высота изображения, начиная с которой будет наложен ватермарк

/**
 * Media
 */
$config['max_user_count'] = 50; // Максимальное количество загрузок для пользователя
$config['max_size'] = 3*1024; // Максимальный размер файла в kB
$config['max_count_files'] = 30; // Максимальное количество файлов медиа у одного объекта
$config['image']['max_size'] = 1*5000; // Максимальный размер файла изображения в kB
$config['image']['autoresize'] = true; // Разрешает автоматическое создание изображений нужного размера при их запросе
$config['image']['original'] = '1500x'; // Размер для хранения оригинала. Если true, то будет сохраняться исходный оригинал без ресайза. Если false, то оригинал сохраняться не будет
$config['image']['sizes'] = array(  // список размеров, которые необходимо делать при загрузке изображения
    
    array(
        'w'    => 500,
        'h'    => null,
        'crop' => false,
    ),
    array(
        'w'    => 100,
        'h'    => 100,
        'crop' => true,
    ),
    
);
$config['image']['preview']['sizes'] = array(  // список размеров, которые необходимо делать при создании превью
    array(
        'w'    => 900,
        'h'    => 300,
        'crop' => true,
    ),
    array(
        'w'    => 250,
        'h'    => 150,
        'crop' => true,
    ),
);


return $config;
<?php

return [
    'library' => [
        'upload' => 'Загрузить',
        'library' => 'Библиотека',
        'sort' => [
            'text' => 'Сортировать:',
            'date_desc' => 'сначала новые',
            'date_asc' => 'сначала старые',
        ],
        'author' => 'автор',
        'filter_type' => [
            'image' => 'изображения'
        ],
        'modal' => [
            'header' => 'Медиафайлы'
        ],
        'button' => [
            'tooltip' => 'Вставить медиа'
        ],
        'button_select' => [
            'text' => 'Вставить'
        ]
    ],
    'admin' => [
        'nav' => [
            'media' => 'Медиабиблиотека'
        ]
    ],
    'media' => [
        'notices' => [
            'error_validate_exists' => 'Уже есть в базе'
            
        ],
        'type' => [
            'image' => 'Картинка',
            'video' => 'Видео'
        ],
        'added' => 'Добавлено'
    ],
    'uploader' => [
        'label' => "Загрузить",
        'max_size' => 'Максимальный размер %%max_size%%Kb',
        'notices' => [
            'error_too_large' => 'Превышен максимальный размер загружаемого файла %%size%%',
            'error_no_file' => 'Файл не загружен',
            'error_no_type' => 'Тип файла %%type%% запрещен',
            'error_upload' => 'Ошибка загрузки',
            'error_upload_count' => 'Достигнут лимит загрузок  %%count%%' 
        ]
    ]
    
];
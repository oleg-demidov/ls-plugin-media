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
        'modal_insert' => [
            'header' => 'Вставка медиафайла'
        ],
        'button' => [
            'tooltip' => 'Вставить медиа'
        ],
        'button_select' => [
            'text' => 'Вставить'
        ],
        'button_clear' => [
            'text' => 'Очистить'
        ],
        'button_load' => [
            'text' => 'Подгрузить'
        ],
        'button_insert' => [
            'text' => 'Вставить'
        ],
        'types' => [
            'all' => 'Все',
            'images' => 'Картинки',
            'video' => 'Видео',
            'docs' => 'Документы'
        ],
        'blankslate' => 'Медиафайлов нет',
        'notices' => [
            'error_no_media' => 'Медиа файл не найден'
        ]
    ],
    'admin' => [
        'nav' => [
            'media' => 'Медиабиблиотека'
        ]
    ],
    'media' => [
        'field_label' => 'Медиафайлы:',
        'choose_btn' => 'Выбрать',
        'notices' => [
            'error_validate_exists' => 'Уже есть в базе',
            'error_remove_access' => 'Вы не можете удалить этот файл',
            'error_validate_count_min' => 'Медиафайлов не может быть меньше %%min%%',
            'error_validate_count_max' => 'Медиафайлов не может быть больше %%max%%',
            'error_validate_count' => 'Медиафайлов должно быть от %%min%% до %%max%%'
        ],
        'type' => [
            'image' => 'Картинка',
            'video' => 'Видео'
        ],
        'added' => 'Добавлено',
        'remove' => 'Вы действительно хотите удалить %%name%%?'
    ],
    'avatar' => [
        'field_label' => 'Аватар'
    ],
    'uploader' => [
        'label' => "Загрузить",
        'max_size' => 'Максимальный размер %%max_size%%Kb',
        'notices' => [
            'error_url_dont_work' => 'Нет ответа. Возможно ссылка не рабочая',
            'error_url_headers_type' => 'Нет заголовка Content-Type',
            'error_url_headers_length' => 'Нет заголовка Content-Length',
            'error_url_upload' => 'Не возможно загрузить файл',
            'error_too_large' => 'Превышен максимальный размер загружаемого файла %%size%%',
            'error_no_file' => 'Файл не загружен',
            'error_no_type' => 'Тип файла %%type%% запрещен',
            'error_upload' => 'Ошибка загрузки',
            'error_upload_count' => 'Достигнут лимит загрузок  %%count%%' 
        ]
    ]
    
];
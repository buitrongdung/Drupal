<?php

/**
 * Implements hook_views_data().
 */
function domynic_view_views_data()
{
    $view['views']['domynic_view_area'] = [
        'title'  => 'Domynic View header',
        'help'   => t('Add unrestricted, custom text or CSS markup.'),
        'area'   => [
            'id' => 'domynic_view_area'
        ]
    ];

    $view['views']['domynic_view_content'] = [
        'field' => [
            'title' => t('Domynic View content'),
            'help'  => t('Unrestricted, custom text or CSS markup.'),
            'id'    => 'domynic_view_content',
            'click sortable' => FALSE,
        ]
    ];

    return $view;
}
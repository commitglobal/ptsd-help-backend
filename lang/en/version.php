<?php

declare(strict_types=1);

return [
    'status' => [
        'drafted' => 'Draft',
        'published' => 'Published',
        'archived' => 'Archived',
    ],

    'label' => [
        'singular' => 'Verion',
        'plural' => 'Versions',

    ],

    'field' => [
        'name' => 'Name',
        'status' => 'Status',
        'countries' => 'Countries',
        'published_at' => 'Published at'
    ],

    'action' => [
        'change_status' => [
            'draft' => [
                'heading' => 'Draft version',
                'subheading' => 'Are you sure you want to move the version to draft?',
                'button' => 'Move to Draft',
                'success' => 'Version was drafted.',
            ],
            'publish' => [
                'heading' => 'Publish version',
                'subheading' => 'Are you sure you want to publish the version?',
                'button' => 'Publish',
                'success' => 'Version was published.',
            ],
            'archive' => [
                'heading' => 'Archive version',
                'subheading' => 'Are you sure you want to archive the version?',
                'button' => 'Archive',
                'success' => 'Version was archived.',
            ],
        ]
    ]
];

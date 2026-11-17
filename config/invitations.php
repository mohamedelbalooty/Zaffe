<?php

return [
    'images' => [
        'max_file_size_kb' => (int) env('INVITATION_IMAGE_MAX_KB', 5120),
        'max_gallery_photos' => (int) env('INVITATION_MAX_GALLERY_PHOTOS', 20),
        'allowed_extensions' => ['jpeg', 'jpg', 'png', 'webp'],
        'allowed_mime_types' => [
            'image/jpeg',
            'image/png',
            'image/webp',
        ],
        'webp_quality' => (int) env('INVITATION_WEBP_QUALITY', 82),
        'cover_max_width' => 1600,
        'gallery_max_width' => 1200,
        'thumbnail_max_width' => 400,
        'temp_disk' => 'local',
        'temp_dir' => 'invitations/tmp',
        'public_disk' => 'public',
    ],
];

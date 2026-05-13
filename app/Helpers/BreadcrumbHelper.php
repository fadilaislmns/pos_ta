<?php

namespace App\Helpers;

class BreadcrumbHelper
{
    public static function generate()
    {
        $segments = request()->segments();

        $breadcrumb = [];

        // Selalu mulai dari dashboard
        $breadcrumb[] = [
            'name' => 'Dashboard',
            'url' => url('/dashboard')
        ];

        $path = '';

        foreach ($segments as $segment) {

            $path .= '/' . $segment;

            // Skip dashboard biar gak dobel
            if ($segment == 'dashboard') continue;

            // Format nama
            $name = ucfirst(str_replace('-', ' ', $segment));

            // Kalau angka → anggap detail
            if (is_numeric($segment)) {
                $name = 'Detail';
            }

            $breadcrumb[] = [
                'name' => $name,
                'url' => url($path)
            ];
        }

        return $breadcrumb;
    }
}

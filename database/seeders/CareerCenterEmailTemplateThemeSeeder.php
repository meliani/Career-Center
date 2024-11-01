<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Visualbuilder\EmailTemplates\Models\EmailTemplateTheme;

class CareerCenterEmailTemplateThemeSeeder extends Seeder
{
    public function run()
    {
        $themes = [
            [
                'name' => 'Professional White',
                'colours' => [
                    'header_bg_color' => '#F8F9FA',
                    'content_bg_color' => '#FFFFFF',
                    'body_bg_color' => '#F8F9FA',
                    'body_color' => '#212529',
                    'footer_bg_color' => '#FFFFFF',
                    'footer_color' => '#6C757D',
                    'callout_bg_color' => '#E9ECEF',
                    'callout_color' => '#212529',
                    'button_bg_color' => '#0D6EFD',
                    'button_color' => '#FFFFFF',
                    'anchor_color' => '#0D6EFD',
                ],
                'is_default' => 1,
            ],
            [
                'name' => 'Corporate Blue',
                'colours' => [
                    'header_bg_color' => '#F8F9FA',
                    'content_bg_color' => '#FFFFFF',
                    'body_bg_color' => '#FFFFFF',
                    'body_color' => '#2C3E50',
                    'footer_bg_color' => '#F8F9FA',
                    'footer_color' => '#6C757D',
                    'callout_bg_color' => '#E3F2FD',
                    'callout_color' => '#1565C0',
                    'button_bg_color' => '#1565C0',
                    'button_color' => '#FFFFFF',
                    'anchor_color' => '#1565C0',
                ],
                'is_default' => 0,
            ],
            [
                'name' => 'Minimal Slate',
                'colours' => [
                    'header_bg_color' => '#37474F',
                    'content_bg_color' => '#FFFFFF',
                    'body_bg_color' => '#FFFFFF',
                    'body_color' => '#263238',
                    'footer_bg_color' => '#37474F',
                    'footer_color' => '#FFFFFF',
                    'callout_bg_color' => '#ECEFF1',
                    'callout_color' => '#263238',
                    'button_bg_color' => '#546E7A',
                    'button_color' => '#FFFFFF',
                    'anchor_color' => '#455A64',
                ],
                'is_default' => 0,
            ],
            [
                'name' => 'Tech Accent',
                'colours' => [
                    'header_bg_color' => '#FFFFFF',
                    'content_bg_color' => '#FFFFFF',
                    'body_bg_color' => '#FAFAFA',
                    'body_color' => '#212121',
                    'footer_bg_color' => '#FFFFFF',
                    'footer_color' => '#757575',
                    'callout_bg_color' => '#E8F5E9',
                    'callout_color' => '#2E7D32',
                    'button_bg_color' => '#00C853',
                    'button_color' => '#FFFFFF',
                    'anchor_color' => '#00C853',
                ],
                'is_default' => 0,
            ],
            [
                'name' => 'Warm Professional',
                'colours' => [
                    'header_bg_color' => '#FFFFFF',
                    'content_bg_color' => '#FFFFFF',
                    'body_bg_color' => '#FFF8E1',
                    'body_color' => '#3E2723',
                    'footer_bg_color' => '#FFFFFF',
                    'footer_color' => '#795548',
                    'callout_bg_color' => '#FFF3E0',
                    'callout_color' => '#E65100',
                    'button_bg_color' => '#FB8C00',
                    'button_color' => '#FFFFFF',
                    'anchor_color' => '#F57C00',
                ],
                'is_default' => 0,
            ],
        ];

        EmailTemplateTheme::factory()
            ->createMany($themes);
    }
}

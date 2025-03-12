<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Repeater;

function saraghaz_register_controls($widget) {
    $widget->start_controls_section(
        'content_section',
        [
            'label' => __('Content', 'text_domain'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]
    );

    $repeater = new Repeater();

    $repeater->add_control(
        'calendar_type',
        [
            'label' => __('Select Calendar Type', 'text_domain'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'hejri_shamsi' => __('هجری شمسی', 'text_domain'),
                'padeshahi' => __('شاهنشاهی', 'text_domain'),
                'maadi' => __('کردی', 'text_domain'),
                'elami' => __('ایلامی', 'text_domain'),
                'zartoshti' => __('زرتشتی', 'text_domain'),
                'iran_nov' => __('ایران نو', 'text_domain'),
                'miladi' => __('میلادی', 'text_domain'),
            ],
            'default' => 'hejri_shamsi',
        ]
    );
// Add this link control after other controls:
$repeater->add_control(
    'calendar_link',
    [
        'label' => __('Calendar Link', 'text_domain'),
        'type' => Controls_Manager::URL,
        'placeholder' => __('https://your-link.com', 'text_domain'),
        'default' => [
            'url' => '',
            'is_external' => true,
            'nofollow' => true,
        ],
        'separator' => 'before',
        'show_external' => true,
        'description' => __('Add a link to make the calendar item clickable.', 'text_domain'),
    ]
);
    $repeater->add_control(
        'background_color',
        [
            'label' => __('Background Color', 'text_domain'),
            'type' => Controls_Manager::COLOR,
            'default' => '#333863',
        ]
    );

    $repeater->add_control(
        'font_color',
        [
            'label' => __('Font Color', 'text_domain'),
            'type' => Controls_Manager::COLOR,
            'default' => '#FFFFFF',
        ]
    );

    $repeater->add_control(
        'height',
        [
            'label' => __('Height (px)', 'text_domain'),
            'type' => Controls_Manager::NUMBER,
            'default' => 50,
            'min' => 50,
            'max' => 1000,
        ]
    );

    $repeater->add_control(
        'background_image',
        [
            'label' => __('Background Image', 'text_domain'),
            'type' => Controls_Manager::MEDIA,
            'default' => [
                'url' => '',
            ],
        ]
    );

    $repeater->add_control(
        'background_image_opacity',
        [
            'label' => __('Background Image Opacity', 'text_domain'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => 0.5,
                'unit' => '',
            ],
            'range' => [
                '' => [
                    'min' => 0,
                    'max' => 1,
                    'step' => 0.1,
                ],
            ],
        ]
    );

    $repeater->add_control(
        'background_image_position',
        [
            'label' => __('Background Image Position', 'text_domain'),
            'type' => Controls_Manager::SELECT,
            'default' => 'center center',
            'options' => [
                'left top' => __('Left Top', 'text_domain'),
                'left center' => __('Left Center', 'text_domain'),
                'left bottom' => __('Left Bottom', 'text_domain'),
                'center top' => __('Center Top', 'text_domain'),
                'center center' => __('Center Center', 'text_domain'),
                'center bottom' => __('Center Bottom', 'text_domain'),
                'right top' => __('Right Top', 'text_domain'),
                'right center' => __('Right Center', 'text_domain'),
                'right bottom' => __('Right Bottom', 'text_domain'),
            ],
        ]
    );

    $repeater->add_control(
        'background_image_size',
        [
            'label' => __('Background Image Size', 'text_domain'),
            'type' => Controls_Manager::SELECT,
            'default' => 'cover',
            'options' => [
                'auto' => __('Auto', 'text_domain'),
                'cover' => __('Cover', 'text_domain'),
                'contain' => __('Contain', 'text_domain'),
            ],
        ]
    );
    
    $repeater->add_control(
        'background_image_border_radius',
        [
            'label' => __('Background Image Border Radius', 'text_domain'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => 0,
                'unit' => 'px',
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                ],
                '%' => [
                    'min' => 0,
                    'max' => 50,
                    'step' => 1,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .your-class-name' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ]
    );

    $repeater->add_control(
        'font_family',
        [
            'label' => __('Font Family', 'text_domain'),
            'type' => Controls_Manager::FONT,
            'default' => 'Digi Hamishe Bold',
        ]
    );

    $repeater->add_responsive_control(
        'font_size',
        [
            'label' => __('Font Size (em)', 'text_domain'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['em'],
            'default' => [
                'size' => 1.5,
                'unit' => 'em',
            ],
            'range' => [
                'em' => [
                    'min' => 0.5,
                    'max' => 5,
                    'step' => 0.1,
                ],
            ],
            'devices' => ['desktop', 'tablet', 'mobile'],
            'desktop_default' => [
                'size' => 1.5,
                'unit' => 'em',
            ],
            'tablet_default' => [
                'size' => 1.2,
                'unit' => 'em',
            ],
            'mobile_default' => [
                'size' => 1,
                'unit' => 'em',
            ],
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}} p' => 'font-size: {{SIZE}}{{UNIT}};',
            ],
        ]
    );

    $widget->add_control(
        'calendars',
        [
            'label' => __('Calendars', 'text_domain'),
            'type' => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => [
                [
                    'calendar_type' => 'hejri_shamsi',
                    'background_color' => '#333863',
                    'font_color' => '#FFFFFF',
                    'height' => 50,
                ],
            ],
            'title_field' => '{{{ calendar_type }}}',
        ]
    );


    $widget->add_control(
        'columns',
        [
            'label' => __('Number of Columns', 'text_domain'),
            'type' => Controls_Manager::NUMBER,
            'default' => 3,
            'min' => 1,
            'max' => 6,
        ]
    );

    $widget->add_control(
        'gap',
        [
            'label' => __('Gap Between Calendars (px)', 'text_domain'),
            'type' => Controls_Manager::NUMBER,
            'default' => 10,
            'min' => 0,
            'max' => 50,
        ]
    );

    $widget->add_control(
        'border_radius',
        [
            'label' => __('Border Radius', 'text_domain'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'default' => [
                'top' => 12,
                'right' => 12,
                'bottom' => 12,
                'left' => 12,
                'unit' => 'px',
                'isLinked' => true,
            ],
            'selectors' => [
                '{{WRAPPER}} .calendar-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    $widget->add_control(
        'date_position',
        [
            'label' => __('Date Position', 'text_domain'),
            'type' => Controls_Manager::SELECT,
            'default' => 'center center',
            'options' => [
                'top left' => __('Top Left', 'text_domain'),
                'top center' => __('Top Center', 'text_domain'),
                'top right' => __('Top Right', 'text_domain'),
                'center left' => __('Center Left', 'text_domain'),
                'center center' => __('Center Center', 'text_domain'),
                'center right' => __('Center Right', 'text_domain'),
                'bottom left' => __('Bottom Left', 'text_domain'),
                'bottom center' => __('Bottom Center', 'text_domain'),
                'bottom right' => __('Bottom Right', 'text_domain'),
            ],
        ]
    );

    $widget->end_controls_section();
}
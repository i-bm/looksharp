<?php

function getServices()
{
    return [
        [
            'name' => 'Strategic Business Consulting',
            'short_name' => 'Strategy Consulting',
            'catchy_name' => 'Smart Strategies for Real Growth',
            'description' => '',
            'route' => route('services.consulting'),
            'image' => 'assets/images/services/consulting.jpg',
        ],
        [
            'name' => 'Custom Software & Web Solutions',
            'short_name' => 'Software Development',
            'catchy_name' => 'Digital Solutions, Built Your Way',
            'description' => '',
            'route' => route('services.software'),
            'image' => 'assets/images/services/software.jpg',
        ],
        [
            'name' => 'IT Infrastructure & Support',
            'short_name' => 'IT Infrastructure',
            'catchy_name' => 'Reliable IT, Built to Last',
            'description' => '',
            'route' => route('services.it-infrastructure'),
            'image' => 'assets/images/services/it-infrastructure.jpg',
        ],
        [
            'name' => 'AI-Powered Business Analytics',
            'short_name' => 'AI Analytics',
            'catchy_name' => 'AI-Powered Insights, Future-Proof Your Business',
            'description' => '',
            'route' => route('services.ai-analytics'),
            'image' => 'assets/images/services/ai-analytics.jpg',
        ],
        [
            'name' => 'Cybersecurity & Compliance',
            'short_name' => 'Cybersecurity',
            'catchy_name' => 'Secure Your Business, Secure Your Future',
            'description' => '',
            'route' => route('services.cybersecurity'),
            'image' => 'assets/images/services/cybersecurity.jpg',
        ],

    ];

}

function getSlider()
{
    return [
        [
            'image' => 'assets/images/slider/consulting_1.jpg',
            'title' => getServices()[0]['catchy_name'],
            'description' => 'We provide strategic business consulting services to help your business grow and succeed.',
            'route' => route('services.consulting'),
        ],
        [
            'image' => 'assets/images/slider/software.jpg',
            'title' => getServices()[1]['catchy_name'],
            'description' => 'We develop custom software and web solutions to help your business grow and succeed. We are a team of experienced developers who are dedicated to providing the best possible solutions to our clients.',
            'route' => route('services.software'),
        ],
        [
            'image' => 'assets/images/slider/it_infrastructure_1.jpg',
            'title' => getServices()[2]['catchy_name'],
            'description' => 'We provide IT infrastructure and support to help your business grow and succeed.',
            'route' => route('services.it-infrastructure'),
        ],
        // [
        //     'image' => 'assets/images/slider/ai.jpg',
        //     'title' => getServices()[3]['catchy_name'],
        //     'description' => 'We provide AI-powered business analytics to help your business grow and succeed.',
        //     'route' => route('services.ai-analytics'),
        // ],
        [
            'image' => 'assets/images/slider/cybersecurity.jpg',
            'title' => getServices()[4]['catchy_name'],
            'description' => 'We provide cybersecurity and compliance to help your business grow and succeed. We are a team of experienced cybersecurity professionals who are dedicated to providing the best possible solutions to our clients.',
            'route' => route('services.cybersecurity'),
        ],
    ];
}

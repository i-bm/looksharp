<?php

namespace Database\Seeders;

use App\Models\Industry;
use Illuminate\Database\Seeder;

class IndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $industries = [
            [
                'name' => 'Legal',
                'description' => 'Legal services, law firms, and legal consultation services.',
            ],
            [
                'name' => 'Estate/Housing',
                'description' => 'Real estate development, property management, and housing services.',
            ],
            [
                'name' => 'Media',
                'description' => 'Media production, broadcasting, publishing, and digital media services.',
            ],
            [
                'name' => 'Transport/Aerospace',
                'description' => 'Transportation services, logistics, aviation, and aerospace industries.',
            ],
            [
                'name' => 'Utilities',
                'description' => 'Public utilities including water, electricity, gas, and telecommunications services.',
            ],
            [
                'name' => 'Education',
                'description' => 'Educational institutions, training services, and educational technology.',
            ],
            [
                'name' => 'Shipping & Port',
                'description' => 'Maritime shipping, port operations, and logistics services.',
            ],
            [
                'name' => 'Tourism',
                'description' => 'Travel services, tour operators, and tourism-related businesses.',
            ],
            [
                'name' => 'Quarry / Mining',
                'description' => 'Extraction of minerals, metals, and other natural resources from the earth.',
            ],
            [
                'name' => 'Hospitality Fashion/Beautification',
                'description' => 'Fashion, beauty, cosmetics, and hospitality services.',
            ],
            [
                'name' => 'Insurance',
                'description' => 'Insurance services including life, health, property, and casualty insurance.',
            ],
            [
                'name' => 'Entertainment',
                'description' => 'Media, arts, recreation, and entertainment services and productions.',
            ],
            [
                'name' => 'Health Care',
                'description' => 'Medical services, healthcare facilities, and health-related services.',
            ],
            [
                'name' => 'Refinery of Minerals',
                'description' => 'Processing and refining of minerals and metals into usable materials.',
            ],
            [
                'name' => 'Agriculture',
                'description' => 'The practice of cultivating plants and livestock for food, fiber, and other products.',
            ],
            [
                'name' => 'Food Industry',
                'description' => 'Production, processing, and manufacturing of food products.',
            ],
            [
                'name' => 'Securities/Brokers',
                'description' => 'Securities trading, brokerage services, and investment advisory services.',
            ],
            [
                'name' => 'Oil and Gas',
                'description' => 'Exploration, extraction, refining, and distribution of petroleum and natural gas.',
            ],
            [
                'name' => 'Manufacturing',
                'description' => 'The process of converting raw materials into finished goods and products.',
            ],
            [
                'name' => 'Commerce/ Trading',
                'description' => 'Commercial trading, import/export, and business commerce services.',
            ],
            [
                'name' => 'Construction',
                'description' => 'Building, infrastructure development, and construction services.',
            ],
            [
                'name' => 'Pharmaceutical',
                'description' => 'Research, development, and manufacturing of pharmaceutical drugs and medicines.',
            ],
            [
                'name' => 'Banking and Finance',
                'description' => 'Financial institutions providing banking, lending, and financial services.',
            ],
            [
                'name' => 'Telecom/ICT',
                'description' => 'Telecommunications, information and communication technology services.',
            ],
            [
                'name' => 'Security',
                'description' => 'Security services, private security, and safety management.',
            ],
            [
                'name' => 'Sanitation',
                'description' => 'Waste management, cleaning services, and environmental sanitation.',
            ],
            [
                'name' => 'Others(Please Specify)',
                'description' => 'Other industries not listed above.',
            ],
        ];

        foreach ($industries as $industry) {
            Industry::updateOrCreate(['name' => $industry['name']], $industry);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\CareerInterestArea;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CareerInterestAreasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $categories = [
                [
                    'name' => 'Finance, Management, & Ops',
                    'slug' => 'finance_management_ops',
                    'order' => 1,
                    'children' => [
                        ['name' => 'Accountant', 'slug' => 'accountant', 'order' => 1],
                        ['name' => 'Administrative Assistant/Executive Assistant', 'slug' => 'administrative_assistant', 'order' => 2],
                        ['name' => 'Claims/Insurance', 'slug' => 'claims_insurance', 'order' => 3],
                        ['name' => 'Data Analyst', 'slug' => 'data_analyst', 'order' => 4],
                        ['name' => 'Finance', 'slug' => 'finance', 'order' => 5],
                        ['name' => 'HR Coordinator', 'slug' => 'hr_coordinator', 'order' => 6],
                        ['name' => 'Management', 'slug' => 'management', 'order' => 7],
                        ['name' => 'Recruiter', 'slug' => 'recruiter', 'order' => 8],
                        ['name' => 'Supply Chain Management', 'slug' => 'supply_chain_management', 'order' => 9],
                    ],
                ],
                [
                    'name' => 'Sales & Marketing',
                    'slug' => 'sales_marketing',
                    'order' => 2,
                    'children' => [
                        ['name' => 'Account Manager / Customer Support', 'slug' => 'account_manager', 'order' => 1],
                        ['name' => 'Campus Rep/Brand Ambassador', 'slug' => 'campus_rep', 'order' => 2],
                        ['name' => 'Digital Marketing & Social Media', 'slug' => 'digital_marketing', 'order' => 3],
                        ['name' => 'Event Coordinator', 'slug' => 'event_coordinator', 'order' => 4],
                        ['name' => 'Public Relations', 'slug' => 'public_relations', 'order' => 5],
                        ['name' => 'Sales / Account Executive', 'slug' => 'sales_account_executive', 'order' => 6],
                        ['name' => 'Sales Operations', 'slug' => 'sales_operations', 'order' => 7],
                    ],
                ],
                [
                    'name' => 'Product & Engineering',
                    'slug' => 'product_engineering',
                    'order' => 3,
                    'children' => [
                        ['name' => 'Data Scientist', 'slug' => 'data_scientist', 'order' => 1],
                        ['name' => 'Engineering (Other)', 'slug' => 'engineering_other', 'order' => 2],
                        ['name' => 'Graphic or UI Designer', 'slug' => 'graphic_ui_designer', 'order' => 3],
                        ['name' => 'Product Manager', 'slug' => 'product_manager', 'order' => 4],
                        ['name' => 'Project Manager', 'slug' => 'project_manager', 'order' => 5],
                        ['name' => 'Quality Assurance', 'slug' => 'quality_assurance', 'order' => 6],
                        ['name' => 'Software Engineer', 'slug' => 'software_engineer', 'order' => 7],
                    ],
                ],
                [
                    'name' => 'Service',
                    'slug' => 'service',
                    'order' => 4,
                    'children' => [
                        ['name' => 'Barista', 'slug' => 'barista', 'order' => 1],
                        ['name' => 'Bartender or Wait Staff', 'slug' => 'bartender_wait_staff', 'order' => 2],
                        ['name' => 'Caregiver', 'slug' => 'caregiver', 'order' => 3],
                        ['name' => 'Driver', 'slug' => 'driver', 'order' => 4],
                        ['name' => 'Petcare', 'slug' => 'petcare', 'order' => 5],
                        ['name' => 'Retail Associate', 'slug' => 'retail_associate', 'order' => 6],
                    ],
                ],
                [
                    'name' => 'Arts & Fashion',
                    'slug' => 'arts_fashion',
                    'order' => 5,
                    'children' => [
                        ['name' => 'Architecture', 'slug' => 'architecture', 'order' => 1],
                        ['name' => 'Merchandiser', 'slug' => 'merchandiser', 'order' => 2],
                        ['name' => 'Photography', 'slug' => 'photography', 'order' => 3],
                        ['name' => 'Textile & Apparel Designer', 'slug' => 'textile_apparel_designer', 'order' => 4],
                        ['name' => 'Video Production', 'slug' => 'video_production', 'order' => 5],
                    ],
                ],
                [
                    'name' => 'Health & Science',
                    'slug' => 'health_science',
                    'order' => 6,
                    'children' => [
                        ['name' => 'Exercise Instructor/Sports Coach', 'slug' => 'exercise_instructor', 'order' => 1],
                        ['name' => 'Nurse', 'slug' => 'nurse', 'order' => 2],
                        ['name' => 'Pharmaceuticals', 'slug' => 'pharmaceuticals', 'order' => 3],
                        ['name' => 'Research & Development', 'slug' => 'research_development', 'order' => 4],
                        ['name' => 'Social Work/Psychology', 'slug' => 'social_work_psychology', 'order' => 5],
                    ],
                ],
                [
                    'name' => 'Education',
                    'slug' => 'education',
                    'order' => 7,
                    'children' => [
                        ['name' => 'Camp Counselor', 'slug' => 'camp_counselor', 'order' => 1],
                        ['name' => 'Education Assistant', 'slug' => 'education_assistant', 'order' => 2],
                        ['name' => 'Teacher', 'slug' => 'teacher', 'order' => 3],
                        ['name' => 'Tutor', 'slug' => 'tutor', 'order' => 4],
                    ],
                ],
                [
                    'name' => 'Law & Journalism',
                    'slug' => 'law_journalism',
                    'order' => 8,
                    'children' => [
                        ['name' => 'Copywriter', 'slug' => 'copywriter', 'order' => 1],
                        ['name' => 'Journalist', 'slug' => 'journalist', 'order' => 2],
                        ['name' => 'Legal Assistant or Paralegal', 'slug' => 'legal_assistant', 'order' => 3],
                    ],
                ],
                [
                    'name' => 'Networking Opportunities',
                    'slug' => 'networking_opportunities',
                    'order' => 9,
                    'children' => [
                        ['name' => 'Events', 'slug' => 'events', 'order' => 1],
                        ['name' => 'Pipeline', 'slug' => 'pipeline', 'order' => 2],
                        ['name' => 'Looksharp', 'slug' => 'looksharp', 'order' => 3],
                    ],
                ],
                [
                    'name' => 'Maintenance & Technicians',
                    'slug' => 'maintenance_technicians',
                    'order' => 10,
                    'children' => [
                        ['name' => 'Technician', 'slug' => 'technician', 'order' => 1],
                    ],
                ],
            ];

            foreach ($categories as $categoryData) {
                $children = $categoryData['children'];
                unset($categoryData['children']);

                $parent = CareerInterestArea::create($categoryData);

                foreach ($children as $childData) {
                    CareerInterestArea::create([
                        'name' => $childData['name'],
                        'slug' => $childData['slug'],
                        'parent_id' => $parent->id,
                        'order' => $childData['order'],
                        'is_active' => true,
                    ]);
                }
            }
        });
    }
}

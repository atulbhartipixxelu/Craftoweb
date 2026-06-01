<?php

namespace Database\Seeders;

use App\Models\DailyUpdate;
use App\Models\Project;
use Illuminate\Database\Seeder;

class DashboardSeeder extends Seeder
{
    public function run(): void
    {
        if (Project::exists()) {
            return;
        }

        $activeProjects = [
            [
                'name' => 'School Portal Redesign',
                'client' => 'Green Valley School',
                'technology' => 'React.js',
                'start_date' => '2025-03-01',
                'status' => 'active',
                'priority' => 'high',
                'progress' => 75,
                'value' => '$12,500',
            ],
            [
                'name' => 'E-commerce Store',
                'client' => 'ShopMax Ltd',
                'technology' => 'Shopify',
                'start_date' => '2025-04-10',
                'status' => 'active',
                'priority' => 'medium',
                'progress' => 45,
                'value' => '$8,200',
            ],
            [
                'name' => 'Corporate Website',
                'client' => 'FinCorp Inc',
                'technology' => 'WordPress',
                'start_date' => '2025-04-20',
                'status' => 'active',
                'priority' => 'high',
                'progress' => 60,
                'value' => '$15,000',
            ],
            [
                'name' => 'Portfolio Website',
                'client' => 'John Designer',
                'technology' => 'Next.js',
                'start_date' => '2025-05-01',
                'status' => 'pending',
                'priority' => 'low',
                'progress' => 20,
                'value' => '$6,800',
            ],
        ];

        $topProjects = [
            [
                'name' => 'Finance',
                'client' => 'FinCorp',
                'technology' => 'WordPress',
                'start_date' => '2024-01-15',
                'status' => 'completed',
                'priority' => 'medium',
                'progress' => 100,
                'value' => '$12,500',
            ],
            [
                'name' => 'Ecommerce',
                'client' => 'ShopMax',
                'technology' => 'WordPress',
                'start_date' => '2024-02-20',
                'status' => 'completed',
                'priority' => 'medium',
                'progress' => 100,
                'value' => '$8,200',
            ],
            [
                'name' => 'Health',
                'client' => 'MediCare',
                'technology' => 'WordPress',
                'start_date' => '2024-03-10',
                'status' => 'completed',
                'priority' => 'medium',
                'progress' => 100,
                'value' => '$15,000',
            ],
            [
                'name' => 'Education',
                'client' => 'EduTech',
                'technology' => 'WordPress',
                'start_date' => '2024-04-05',
                'status' => 'completed',
                'priority' => 'medium',
                'progress' => 100,
                'value' => '$9,800',
            ],
        ];

        $projects = [];
        foreach (array_merge($activeProjects, $topProjects) as $data) {
            $projects[] = Project::create($data);
        }

        $recentUpdates = [
            [
                'project_keyword' => 'School',
                'date' => '2025-05-29',
                'description' => 'Updated homepage slider and navigation menu',
                'hours' => 3,
            ],
            [
                'project_keyword' => 'E-commerce',
                'date' => '2025-05-28',
                'description' => 'Added payment gateway integration',
                'hours' => 5,
            ],
            [
                'project_keyword' => 'Corporate',
                'date' => '2025-05-27',
                'description' => 'Fixed responsive layout issues on mobile',
                'hours' => 2,
            ],
        ];

        foreach ($recentUpdates as $update) {
            $project = collect($projects)->first(
                fn (Project $p) => str_contains(strtolower($p->name), strtolower($update['project_keyword']))
            ) ?? $projects[0];

            DailyUpdate::create([
                'project_id' => $project->id,
                'date' => $update['date'],
                'description' => $update['description'],
                'hours' => $update['hours'],
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Placeholder services — replace with the client's real list via /admin.
     */
    public function run(): void
    {
        $services = [
            ['name' => 'Consulting', 'description' => 'Expert advice tailored to your business.'],
            ['name' => 'Installation', 'description' => 'On-site installation and setup.'],
            ['name' => 'Maintenance & Support', 'description' => 'Ongoing maintenance and technical support.'],
            ['name' => 'Training', 'description' => 'Hands-on training for your team.'],
        ];

        foreach ($services as $i => $service) {
            Service::firstOrCreate(['name' => $service['name']], [...$service, 'sort_order' => $i]);
        }
    }
}

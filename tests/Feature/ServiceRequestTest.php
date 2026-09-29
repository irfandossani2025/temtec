<?php

namespace Tests\Feature;

use App\Mail\ServiceRequestReceived;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ServiceRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_dashboard_shows_their_requests(): void
    {
        $user = User::factory()->create(['company_name' => 'Acme Ltd']);
        $request = $user->serviceRequests()->create(['notes' => 'Hello']);
        $request->services()->attach(Service::create(['name' => 'Consulting']));

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertSee('Acme Ltd')
            ->assertSee('Consulting');
    }

    public function test_client_can_request_more_services(): void
    {
        Mail::fake();
        config(['temtec.sales_emails' => ['sales@example.com']]);
        $user = User::factory()->create();
        $service = Service::create(['name' => 'Training']);

        $this->actingAs($user)->get('/service-requests/create')->assertOk()->assertSee('Training');

        $this->actingAs($user)
            ->post('/service-requests', ['services' => [$service->id], 'notes' => 'More please'])
            ->assertRedirect('/dashboard');

        $this->assertSame([$service->id], $user->serviceRequests()->sole()->services->pluck('id')->all());
        Mail::assertSent(ServiceRequestReceived::class);
    }

    public function test_guests_cannot_request_services(): void
    {
        $this->post('/service-requests', ['services' => [1]])->assertRedirect('/login');
    }

    public function test_only_admins_can_access_admin_panel(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();

        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($admin)->get('/admin/service-requests')->assertOk();
        $this->actingAs($admin)->get('/admin/services')->assertOk();
        $this->actingAs($admin)->get('/admin/users')->assertOk();
    }

    public function test_admin_can_view_a_service_request(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();
        $client = User::factory()->create(['company_name' => 'Globex', 'phone' => '555-0100']);
        $request = $client->serviceRequests()->create(['notes' => 'Urgent']);
        $request->services()->attach(Service::create(['name' => 'Installation']));

        $this->actingAs($admin)->get("/admin/service-requests/{$request->id}")
            ->assertOk()
            ->assertSee('Globex')
            ->assertSee('555-0100')
            ->assertSee('Installation');
    }
}

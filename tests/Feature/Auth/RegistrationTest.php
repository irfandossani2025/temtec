<?php

namespace Tests\Feature\Auth;

use App\Enums\ServiceRequestStatus;
use App\Mail\ServiceRequestConfirmation;
use App\Mail\ServiceRequestReceived;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return [
            'company_name' => 'Acme Ltd',
            'name' => 'Test User',
            'job_title' => 'Operations Manager',
            'email' => 'test@example.com',
            'phone' => '+1 (555) 123-4567',
            'website' => 'https://acme.test',
            'address' => '1 Main St',
            'city' => 'Toronto',
            'country' => 'Canada',
            'password' => 'password',
            'password_confirmation' => 'password',
            'notes' => 'We need this by Q1.',
            ...$overrides,
        ];
    }

    public function test_registration_screen_lists_only_active_services(): void
    {
        Service::create(['name' => 'Active Service']);
        Service::create(['name' => 'Hidden Service', 'is_active' => false]);

        $this->get('/register')
            ->assertOk()
            ->assertSee('Active Service')
            ->assertDontSee('Hidden Service');
    }

    public function test_new_users_can_register_with_company_details_and_services(): void
    {
        Mail::fake();
        config(['temtec.sales_emails' => ['sales@example.com', 'boss@example.com']]);
        $services = collect([Service::create(['name' => 'Consulting']), Service::create(['name' => 'Training'])]);

        $response = $this->post('/register', $this->payload(['services' => $services->pluck('id')->all()]));

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::firstWhere('email', 'test@example.com');
        $this->assertSame('Acme Ltd', $user->company_name);
        $this->assertSame('+1 (555) 123-4567', $user->phone);
        $this->assertSame('Canada', $user->country);
        $this->assertFalse($user->is_admin);

        $request = $user->serviceRequests()->sole();
        $this->assertSame(ServiceRequestStatus::New, $request->status);
        $this->assertSame('We need this by Q1.', $request->notes);
        $this->assertEqualsCanonicalizing($services->pluck('id')->all(), $request->services->pluck('id')->all());

        Mail::assertSent(ServiceRequestReceived::class, fn ($mail) => $mail->hasTo('sales@example.com')
            && $mail->hasTo('boss@example.com')
            && $mail->hasReplyTo('test@example.com'));
        Mail::assertSent(ServiceRequestConfirmation::class, fn ($mail) => $mail->hasTo('test@example.com'));
    }

    public function test_registration_requires_company_phone_and_at_least_one_service(): void
    {
        $this->post('/register', $this->payload(['company_name' => '', 'phone' => '', 'services' => []]))
            ->assertSessionHasErrors(['company_name', 'phone', 'services']);

        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_inactive_services_cannot_be_submitted(): void
    {
        $hidden = Service::create(['name' => 'Hidden', 'is_active' => false]);

        $this->post('/register', $this->payload(['services' => [$hidden->id]]))
            ->assertSessionHasErrors('services.0');
    }

    public function test_clients_cannot_make_themselves_admin(): void
    {
        $service = Service::create(['name' => 'Consulting']);

        $this->post('/register', $this->payload(['services' => [$service->id], 'is_admin' => 1]));

        $this->assertFalse(User::firstWhere('email', 'test@example.com')->is_admin);
    }

    public function test_registration_still_succeeds_when_mail_fails(): void
    {
        config(['mail.default' => 'smtp', 'mail.mailers.smtp.host' => '127.0.0.1', 'mail.mailers.smtp.port' => 1, 'temtec.sales_emails' => ['sales@example.com']]);
        $service = Service::create(['name' => 'Consulting']);

        $this->post('/register', $this->payload(['services' => [$service->id]]))
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseCount('service_requests', 1);
    }
}

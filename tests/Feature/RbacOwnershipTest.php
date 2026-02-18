<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacOwnershipTest extends TestCase
{
    use RefreshDatabase;

    private function seedRoles(): void
    {
        // Seederهای پروژه خودت
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_sales_cannot_access_global_activity(): void
    {
        $this->seedRoles();

        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $res = $this->actingAs($sales)->get('/activity');
        $res->assertStatus(403);
    }

    public function test_sales_cannot_view_other_users_customer_show(): void
    {
        $this->seedRoles();

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $adminCustomer = Customer::factory()->create([
            'owner_id' => $admin->id,
        ]);

        $res = $this->actingAs($sales)->get(route('customers.show', $adminCustomer));
        $res->assertStatus(403);
    }

    public function test_sales_cannot_attach_other_users_customer_to_task(): void
    {
        $this->seedRoles();

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $adminCustomer = Customer::factory()->create([
            'owner_id' => $admin->id,
        ]);

        $payload = [
            'title' => 'test task',
            'type' => 'call',
            'status' => 'open',
            'due_at' => now()->addDay()->format('Y-m-d H:i'),
            'customer_id' => $adminCustomer->id, // ❌ attach غیرمجاز
        ];

        $res = $this->actingAs($sales)->post(route('tasks.store'), $payload);
        $res->assertStatus(403);
    }

    public function test_sales_cannot_view_other_users_task_show(): void
    {
        $this->seedRoles();

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $adminTask = Task::factory()->create([
            'assigned_to' => $admin->id,
        ]);

        $res = $this->actingAs($sales)->get(route('tasks.show', $adminTask));
        $res->assertStatus(403);
    }

    public function test_convert_lead_only_once(): void
    {
        $this->seedRoles();

        $sales = User::factory()->create();
        $sales->assignRole('sales');
        // اگر permission leads.convert به sales داده‌ای، این خط لازم نیست.
        // اگر ندادی، اینجا بده تا تست معنی‌دار باشد:
        $sales->givePermissionTo('leads.convert');

        $lead = Lead::factory()->create([
            'owner_id' => $sales->id,
            'status' => 'new',
            'email' => 'same@mail.com',
        ]);

        $res1 = $this->actingAs($sales)->post(route('leads.convert', $lead));
        $res1->assertStatus(302);

        $lead->refresh();
        $this->assertEquals('converted', $lead->status);
        $this->assertNotNull($lead->customer_id);

        $res2 = $this->actingAs($sales)->post(route('leads.convert', $lead));
        $res2->assertStatus(302);
        $res2->assertSessionHas('error');
    }
}

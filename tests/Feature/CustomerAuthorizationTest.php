<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CustomerAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected Role $adminRole;
    protected Role $salesRole;

    protected function setUp(): void
    {
        parent::setUp();

        // ساخت permissionها
        $perms = [
            'customers.view',
            'customers.create',
            'customers.update',
            'customers.delete',
            'dashboard.view',
        ];

        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        // نقش‌ها
        $this->adminRole = Role::firstOrCreate(['name' => 'admin']);
        $this->salesRole = Role::firstOrCreate(['name' => 'sales']);

        // admin همه مجوزها
        $this->adminRole->syncPermissions($perms);

        // sales (طبق تنظیم تو: delete نداره)
        $this->salesRole->syncPermissions([
            'customers.view',
            'customers.create',
            'customers.update',
            // 'customers.delete' intentionally omitted
        ]);
    }

    public function test_sales_only_sees_own_customers_in_index(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $other = User::factory()->create();
        $other->assignRole('admin');

        $mine = Customer::factory()->create(['owner_id' => $sales->id, 'name' => 'Mine']);
        $notMine = Customer::factory()->create(['owner_id' => $other->id, 'name' => 'NotMine']);

        $response = $this->actingAs($sales)->get('/customers');

        $response->assertOk();
        $response->assertSee('Mine');
        $response->assertDontSee('NotMine');
    }

    public function test_sales_cannot_view_or_edit_or_update_other_users_customer(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $otherCustomer = Customer::factory()->create([
        'owner_id' => $admin->id,
    ]);

    $this->actingAs($sales)
        ->get(route('customers.show', $otherCustomer))
        ->assertForbidden();

    $this->actingAs($sales)
        ->get(route('customers.edit', $otherCustomer))
        ->assertForbidden();

    $this->actingAs($sales)
        ->put(route('customers.update', $otherCustomer), [
            'name' => 'Hacked',
            'email' => 'hacked@test.com',
            'phone' => '999',
            'owner_id' => $sales->id,
        ])
        ->assertForbidden();

    }

    public function test_sales_can_update_own_customer_but_owner_id_does_not_change(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $otherUser = User::factory()->create();

        $mine = Customer::factory()->create(['owner_id' => $sales->id]);

        $this->actingAs($sales)
            ->put(route('customers.update', $mine), [
                'name' => 'Updated Name',
                'email' => 'updated@test.com',
                'phone' => '123',
                'owner_id' => $otherUser->id, // تلاش برای تغییر مالک
            ])
            ->assertRedirect(route('customers.index'));

        $mine->refresh();
        $this->assertSame('Updated Name', $mine->name);
        $this->assertSame('updated@test.com', $mine->email);
        $this->assertSame('123', $mine->phone);

        // مالک نباید تغییر کند
        $this->assertSame($sales->id, $mine->owner_id);
    }

    public function test_admin_can_edit_any_customer(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $cust = Customer::factory()->create(['owner_id' => $sales->id]);

        $this->actingAs($admin)
            ->get(route('customers.edit', $cust))
            ->assertOk();

        $this->actingAs($admin)
            ->put(route('customers.update', $cust), [
                'name' => 'Admin Updated',
                'email' => 'adminupdated@test.com',
                'phone' => '777',
                'owner_id' => $admin->id, // اگر update برای admin اجازه تغییر owner می‌دهی
            ])
            ->assertRedirect(route('customers.index'));
    }
}

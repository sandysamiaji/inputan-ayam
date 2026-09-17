<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Support\Facades\Hash;

class AuthAndPermissionTest extends TestCase
{
    /**
     * Test 1: Halaman login publik dapat diakses dengan normal
     */
    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('NOCHI FARM');
        $response->assertSee('Silakan Masuk');
        $response->assertSee('Username / Email');
        $response->assertSee('Password');
    }

    /**
     * Test 2: Login berhasil dengan username & password valid
     */
    public function test_login_success_with_valid_credentials(): void
    {
        $user = User::where('username', 'test_auth_user')->first();
        if (!$user) {
            $user = User::create([
                'name' => 'User Autentikasi Test',
                'username' => 'test_auth_user',
                'email' => 'testauth@nochifarm.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'is_active' => true,
            ]);
        }

        $response = $this->post('/login', [
            'login' => 'test_auth_user',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test 3: Login gagal jika password salah
     */
    public function test_login_fails_with_invalid_password(): void
    {
        $response = $this->from('/login')->post('/login', [
            'login' => 'test_auth_user',
            'password' => 'passwordsalah',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    /**
     * Test 4: Akun non-aktif dicegah untuk login
     */
    public function test_inactive_user_cannot_login(): void
    {
        $inactive = User::where('username', 'inactive_user')->first();
        if (!$inactive) {
            $inactive = User::create([
                'name' => 'Inactive Test',
                'username' => 'inactive_user',
                'email' => 'inactive@nochifarm.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'is_active' => false,
            ]);
        }

        $response = $this->from('/login')->post('/login', [
            'login' => 'inactive_user',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    /**
     * Test 5: Admin memiliki hak akses penuh (Full Access) ke semua fitur
     */
    public function test_admin_has_full_access_to_all_features(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Super Admin Test',
                'username' => 'super_admin_test',
                'email' => 'superadmintest@nochifarm.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
            ]);
        }

        $this->assertTrue($admin->canAccess('menu_dashboard'));
        $this->assertTrue($admin->canAccess('menu_warehouse'));
        $this->assertTrue($admin->canAccess('menu_master'));
        $this->assertTrue($admin->canAccess('feature_quick_egg'));
    }

    /**
     * Test 6: Toggle hak akses user non-admin memblokir akses ke rute terlarang
     */
    public function test_user_is_blocked_when_permission_is_disabled(): void
    {
        $user = User::where('username', 'field_user')->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Field Worker Test',
                'username' => 'field_user',
                'email' => 'fieldworker@nochifarm.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'is_active' => true,
            ]);
        }

        // Matikan akses ke menu gudang
        UserPermission::updateOrCreate(
            ['user_id' => $user->id, 'permission_key' => 'menu_warehouse'],
            ['is_enabled' => false]
        );

        $this->assertFalse($user->canAccess('menu_warehouse'));

        // Coba akses route /gudang
        $response = $this->actingAs($user)->get('/gudang');
        $response->assertRedirect('/');
        $response->assertSessionHas('error');
    }

    /**
     * Test 7: Admin dapat mengubah permission via endpoint AJAX toggle
     */
    public function test_admin_can_toggle_user_permission_via_ajax(): void
    {
        $admin = User::firstOrCreate(
            ['username' => 'admin_test_toggle'],
            [
                'name' => 'Admin Toggle Test',
                'email' => 'admintoggle@nochifarm.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $user = User::firstOrCreate(
            ['username' => 'user_test_toggle'],
            [
                'name' => 'User Toggle Test',
                'email' => 'usertoggle@nochifarm.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'is_active' => true,
            ]
        );

        $response = $this->actingAs($admin)->postJson("/master/hak-akses/{$user->id}/toggle", [
            'permission_key' => 'menu_rekap',
            'is_enabled' => false,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'permission_key' => 'menu_rekap',
            'is_enabled' => false,
        ]);

        $this->assertDatabaseHas('user_permissions', [
            'user_id' => $user->id,
            'permission_key' => 'menu_rekap',
            'is_enabled' => 0,
        ]);
    }

    /**
     * Test 8: Logout berhasil dan mengarahkan kembali ke halaman login
     */
    public function test_logout_redirects_to_login(): void
    {
        $user = User::first();
        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}

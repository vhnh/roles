<?php

namespace Vhnh\Roles\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Vhnh\Roles\HasRoles;

class RolesTest extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }

    protected function create($attributes = [])
    {
        return User::forceCreate(array_merge([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'type' => 'guest',
        ], $attributes));
    }

    /** @test */
    public function it_fetches_all_users_by_its_role()
    {
        $this->create(['email' => 'johndoe@example.com', 'type' => 'admin']);
        $this->create(['email' => 'janedoe@example.com', 'type' => 'guest']);
    
        $this->assertCount(1, Admin::all());
        $this->assertCount(1, Guest::all());
        $this->assertCount(2, User::all());
    }

    /** @test */
    public function it_fetches_a_user_by_its_email()
    {
        $john = $this->create(['email' => 'johndoe@example.com', 'type' => 'admin'])->fresh();

        $this->assertEquals($john, User::firstWhere('email', 'johndoe@example.com'));
        $this->assertEquals($john, Admin::firstWhere('email', 'johndoe@example.com'));
    }

    /** @test */
    public function it_creates_roles()
    {
        Admin::forceCreate([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'type' => 'admin',
        ]);
    }
}

class User extends Model
{
    protected $availableRoles = [
        'admin' => Admin::class,
        'guest' => Guest::class,
    ];

    protected $fillable = [
        'type',
    ];

    protected $table = 'users';

    use HasRoles;
}

class Admin extends User
{
    protected static $role = 'admin';
}

class Guest extends User
{
    protected static $role = 'guest';
}

<?php

namespace Tests\Unit;

use App\Exceptions\UndefinedUserStateException;
use App\Group;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionClass;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function userHasGroups()
    {
        $user = factory(User::class)->create();
        $excludedGroups = factory(Group::class, 3)->create();
        $includedGroups = factory(Group::class, 2)->create();

        foreach ($includedGroups as $group) {
            $user->groups()->save($group);
        }

        $userGroups = $user->groups()->get();
        $this->assertEquals($userGroups->pluck('id'), $includedGroups->pluck('id'));
    }

    /** @test */
    public function userHasStates()
    {
        $constants = (new ReflectionClass(User::class))->getConstants();
        $constantsCollection = collect($constants);

        $filtered = $constantsCollection->filter(function($value, $key) {
            return mb_strpos($key, '_STATE') === mb_strlen($key) - 6;
        });

        $expected = [
            'ACTIVE_STATE' => 'active',
            'NON_ACTIVE_STATE' => 'non active'
        ];
        $actual = $filtered->toArray();
        $this->assertEquals($expected, User::states());
    }

    /** @test */
    public function setDefinedStateAttribute()
    {
        $user = factory(User::class)->create();
        $this->assertEquals(User::NON_ACTIVE_STATE, $user->getAttribute('state'));

        $user->state = User::ACTIVE_STATE;

        $this->assertEquals(User::ACTIVE_STATE, $user->getAttribute('state'));
    }

    /** @test */
    public function setUndefinedStateAttribute()
    {
        $user = factory(User::class)->create();

        try {
            $user->state = 'undefined state';
        } catch (UndefinedUserStateException $e) {
            $this->assertTrue(true);
            return;
        }

        $this->fail("UndefinedUserStateException didn't throw");
    }
}

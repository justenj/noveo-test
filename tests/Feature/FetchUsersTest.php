<?php

namespace Tests\Feature;

use App\User;
use Tests\PaginationTesting;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FetchUsersTest extends TestCase
{
    use RefreshDatabase;
    use PaginationTesting;

    private $modelClass = User::class;
    private $urlPath = 'users';

}

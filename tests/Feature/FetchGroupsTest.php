<?php

namespace Tests\Feature;

use App\Group;
use Tests\PaginationTesting;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FetchGroupTest extends TestCase
{
    use RefreshDatabase;
    use PaginationTesting;

    private $modelClass = Group::class;
    private $urlPath = 'groups';

}

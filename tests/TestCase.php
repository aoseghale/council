<?php

namespace Tests;

use App\Exceptions\TestHandler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp()
    {
        parent::setUp();

        DB::statement('PRAGMA foreign_keys=ON;');

        $this->disableExceptionHandling();
    }

    protected function signIn($user = null)
    {
        $user = $user ?: create('App\User');

        $this->be($user);
        // $this->actingAs($user);

        return $this;
    }

    protected function signInAdmin($admin = null)
    {
        $admin = $admin ?: create('App\User');

        config(['council.administrators' => [$admin->email]]);

        $this->actingAs($admin);

        return $this;
    }

    protected function disableExceptionHandling()
    {
        $this->oldExceptionHandler = $this->app->make(ExceptionHandler::class);

        $this->app->instance(ExceptionHandler::class, new TestHandler);
    }

    protected function withExceptionHandling()
    {
        $this->app->instance(ExceptionHandler::class, $this->oldExceptionHandler);

        return $this;
    }
}

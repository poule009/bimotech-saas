<?php

namespace Tests;

use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Les permissions granulaires Spatie (cf. PermissionsSeeder) doivent exister
     * dès qu'un test réinitialise la base : le middleware CheckAgencyPermission et
     * User::hasAgencyPermission() s'appuient dessus. Sans elles, toute route protégée
     * par `agency.can:` renvoyait une erreur 500 (PermissionDoesNotExist).
     *
     * On ne seede que pour les tests utilisant RefreshDatabase (les tables existent),
     * jamais pour les tests purs sans base.
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (in_array(RefreshDatabase::class, class_uses_recursive(static::class), true)) {
            $this->seed(PermissionsSeeder::class);
        }
    }
}

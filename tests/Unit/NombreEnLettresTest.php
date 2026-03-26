<?php

namespace Tests\Unit;

use App\Services\NombreEnLettres;
use Tests\TestCase;

class NombreEnLettresTest extends TestCase
{
    public function test_zero(): void
    {
        $this->assertEquals('Zéro FCFA', NombreEnLettres::convertir(0));
    }

    public function test_nombres_simples(): void
    {
        $this->assertEquals('Un FCFA',    NombreEnLettres::convertir(1));
        $this->assertEquals('Dix FCFA',   NombreEnLettres::convertir(10));
        $this->assertEquals('Vingt FCFA', NombreEnLettres::convertir(20));
    }

    public function test_nombres_complexes(): void
    {
        $this->assertEquals('Quatre-vingt-dix-neuf FCFA', NombreEnLettres::convertir(99));
        $this->assertEquals('Cent FCFA',                  NombreEnLettres::convertir(100));
        $this->assertEquals('Deux cents FCFA',            NombreEnLettres::convertir(200));
        $this->assertEquals('Deux cent un FCFA',          NombreEnLettres::convertir(201));
    }

    public function test_milliers(): void
    {
        $this->assertEquals('Mille FCFA',                         NombreEnLettres::convertir(1000));
        $this->assertEquals('Deux mille FCFA',                    NombreEnLettres::convertir(2000));
        $this->assertEquals('Deux cent cinquante mille FCFA',     NombreEnLettres::convertir(250000));
        $this->assertEquals('Deux cent vingt mille cinq cents FCFA', NombreEnLettres::convertir(220500));
    }

    public function test_millions(): void
    {
        $this->assertEquals('Un million FCFA',                       NombreEnLettres::convertir(1000000));
        $this->assertEquals('Un million sept cent cinquante mille FCFA', NombreEnLettres::convertir(1750000));
        $this->assertEquals('Deux millions FCFA',                    NombreEnLettres::convertir(2000000));
    }

    public function test_devise_personnalisee(): void
    {
        $this->assertEquals('Deux cent cinquante mille XOF', NombreEnLettres::convertir(250000, 'XOF'));
        $this->assertEquals('Cent EUR',                      NombreEnLettres::convertir(100, 'EUR'));
    }
}
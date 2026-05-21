<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urlsStatiques = [
            ['loc' => route('portail.index'), 'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => url('/'),               'priority' => '0.9', 'changefreq' => 'weekly'],
            ['loc' => route('pricing'),       'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => route('demo'),          'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => route('faq'),           'priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => route('contact'),       'priority' => '0.6', 'changefreq' => 'monthly'],
        ];

        try {
            $biens = Bien::portail()
                ->select(['id', 'slug', 'updated_at'])
                ->orderByDesc('updated_at')
                ->get();
        } catch (\Throwable) {
            $biens = collect();
        }

        $content = view('sitemap', compact('urlsStatiques', 'biens'))->render();

        return response($content, 200)->header('Content-Type', 'application/xml');
    }
}

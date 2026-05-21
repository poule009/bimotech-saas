<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach($urlsStatiques as $url)
    <url>
        <loc>{{ $url['loc'] }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>{{ $url['changefreq'] }}</changefreq>
        <priority>{{ $url['priority'] }}</priority>
    </url>
    @endforeach
    @foreach($biens as $bien)
    <url>
        <loc>{{ route('portail.show', $bien->slug) }}</loc>
        <lastmod>{{ $bien->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach
</urlset>

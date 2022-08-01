@php
    echo '<?xml version="1.0" encoding="UTF-8"?>'
@endphp

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($acquisitionsLists as $acquisitionsList)

        @if($acquisitionsList->url_path !== 'latest')
            <url>
                <loc>{{ url('/') }}/{{ $acquisitionsList->url_path }}</loc>
                <lastmod>{{ $currentDate }}</lastmod>
                <changefreq>daily</changefreq>
                <priority>0.5</priority>
            </url>
        @else
            <url>
                <loc>{{ url('/') }}/{{ $acquisitionsList->url_path }}</loc>
                <lastmod>{{ $currentDate }}</lastmod>
                <changefreq>daily</changefreq>
                <priority>0.8</priority>
            </url>
        @endif
    @endforeach
</urlset>

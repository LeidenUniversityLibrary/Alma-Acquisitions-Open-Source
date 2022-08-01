<?=
    /* Using an echo tag here so the `<? ... ?>` won't get parsed as short tags */
'<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL
?>

<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:media="http://search.yahoo.com/mrss/">
    <channel>
        <title>{{config('app.name') }}</title>
        <link>{{config('app.url') }}</link>
        <description>{{config('app.description') }}</description>
        <pubDate>{{$today}}</pubDate>
    @foreach($acquisitions as $item)
        <item>
            <title><![CDATA[{{ $item->Title }}]]></title>
            <!--NOTE make sure to edit this URL with your institution's details-->
            <link><![CDATA[https://example.com/primo-explore/search?tab=YOUR_TAB&search_scope=Local&vid=YOUR_VID&lang=en_US&offset=0&query=any,exact,{{ $item->{'MMS Id'} }}]]></link>
            <author>{{$item->Author}}</author>
            <publisher><![CDATA[{{$item->Publisher}}]]></publisher>
            <publicationDate>{{ $item->{'Publication Date'} }}</publicationDate>
            <mattype>{{ $item->{'Resource Type'} }}</mattype>
            {{--NOTE pubDate is a standard RSS element. It indicates "the publication date for the content in the channel." which translates to the date we added the item to the Library's collections, not to the publication date of the book. --}}
            <pubDate>{{ $item->{'Creation Date'} }}</pubDate>
            <arrivalDate>{{ $item->{'Creation Date'} }}</arrivalDate>
        </item>
    @endforeach
    </channel>
</rss>

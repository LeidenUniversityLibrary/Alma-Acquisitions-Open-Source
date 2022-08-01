@extends('layouts.app')
@section('css')
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/v/bs5/dt-1.11.3/r-2.2.9/datatables.min.css"/>
@endsection

@section('content')
    {{-- SECTION content --}}
    <div class="row">
        <div class="col" id="main-col">

            <div class="row justify-content-center">
                <div class="col-6">
                    <select class="form-select text-center" aria-label="Default select"
                            onchange="location = this.options[this.selectedIndex].value">

                        <option disabled>Select a subject</option>
                        <option disabled>─ ─ ─ ─ ─ ─ ─ ─ ─ ─</option>


                        @foreach ($acquisitions_lists->sortBy('acquisitions_list_name') as $acquisition_list)

                            @if (Request::path() == $acquisition_list->url_path)
                                <option selected>{{ $acquisition_list->acquisitions_list_name }}</option>
                            @else
                                <option value="{{ url('/'. $acquisition_list->url_path) }}">
                                    {{ $acquisition_list->acquisitions_list_name }}</option>
                            @endif

                        @endforeach
                    </select>
                </div>
                <div class="col-2" id="rss">
                    @if(Request::path() == $acquisitionListTitle)
                        <a href="{{ url('/feed/' . $acquisitionListTitle) }}" target="_blank"><i style="color: #FFA500"
                                                                                                 class="bi bi-rss"></i></a>
                    @endif
                </div>
            </div>
            <hr>

            <table class="table table-hover table-striped" style="width:100%" id="data-table">
                <thead>
                <tr>
                    <th></th>
                    <th data-priority="1" style="min-width: 33%">Title</th>
                    <th data-priority="2">Author</th>
                    <th data-priority="3">Publisher</th>
                    <th>Publication Date</th>
                    <th>Resource Type</th>
                    <th>Acquisition Date</th>
                    <th>Subjects</th>
                    <th>LC Classification</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($acquisitions as $acquisition)


                    <tr>
                        <td class="px-4">
                        </td>
                        <td id='title_col'>
                            <!-- Leiden University Libraries uses this href:
                            https://catalogue.leidenuniv.nl/primo-explore/search?query=any,exact,{{ $acquisition->{'MMS Id'} }}&tab=leiden&search_scope=Local&vid=UBL_V1&lang=en_US&offset=0
                            You must build a similar URL for your institution and your Primo settings-->
                            <a href="https://example.com/primo-explore/search?query=any,exact,{{ $acquisition->{'MMS Id'} }}&tab=YOUR_PRIMO_TAB&search_scope=Local&vid=YOUR_INSTITUTION_VID&lang=en_US&offset=0"
                               target="_blank">{{ $acquisition->Title }}</a>
                        </td>
                        <td id='author_col'>
                            {{ $acquisition->Author }}
                        </td>
                        <td id='publisher_col'>
                            {{ $acquisition->Publisher }}

                        </td>
                        <td id='publication_date_col'>
                            {{ $acquisition->{'Publication Date'} }}

                        </td>
                        <td id='resource_type_col'>
                            {{ $acquisition->{'Resource Type'} }}

                        </td>
                        <td id='creation_date_col' style="display:none">
                            {{ $acquisition->{'Creation Date'} }}

                        </td>
                        <td id='subject_col' style="display:none">
                            {{ $acquisition->Subjects }}

                        </td>
                        <td id='start_range_col' style="display:none">
                            {{ $acquisition->{'Start Range'} }}

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <hr>
        </div>
    </div>

@endsection

@section('javascript')
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.11.5/r-2.2.9/datatables.min.js">
    </script>
    <script type="text/javascript" src="{{ asset('/js/datatable.js') }}"></script>
@endsection

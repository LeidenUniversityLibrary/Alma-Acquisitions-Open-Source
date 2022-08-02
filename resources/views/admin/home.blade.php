@extends('layouts.app')

@section('content')

    <!-- SECTION content -->
    <h2>Acquisitions Lists Overview</h2>
    <label for="acquisition_lists">The items in this list are called from Alma Analytics via the Alma Analytics API. If
        an acquisitions list is missing in this list, make sure it has been created in Alma Analytics first.</label>
    <hr>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('status'))
        <div class="alert alert-warning">
            {{ session('status') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <a href="{{route('force_refresh_database')}}" class="btn btn-primary" role="button"
       id="refresh_databases"><i class="bi bi-arrow-clockwise"></i> Refresh acquisitions
    </a>
    <table class="table table-hover" id="acquisition_lists-table">
        <thead>
        <tr>
            <th>Acquisitions list name in Alma Analytics</th>
            <th>Acquisitions last updated at</th>
            <th>XML last modified at</th>
            <th>Options</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($improvedAcquisitionLists as $list )
            @if($list['value'] === config('acquisitions.homepage_acquisitions_list'))
                {{--Prevents accidental deletion of the main acquisition list--}}
                @if ($list['existsInDatabase'] == FALSE)
                    <tr class="table-secondary">
                        <td>{{$list['value']}}</td>
                        <td>Never</td>
                        <td>Never</td>
                        <td>
                            <a class="btn btn-success" href="{{ url('admin/create/' . $list['value']) }}"><i
                                    class="bi bi-plus-circle"></i> Create</a>
                        </td>
                    </tr>
                @else
                    <tr class="table-success">
                        <td>{{$list['value']}}</td>
                        <td>{{$list['DBLastModifiedAt']}}</td>
                        <td>{{$list['XMLLastModified']}}</td>
                        <td>
                            <a class="btn btn-warning"
                               href="{{ url('admin/edit/' . $list['value']) }}"><i class="bi bi-pencil"></i>
                                Edit</a>
                        </td>
                    </tr>
                @endif
            @else
                @if ($list['existsInDatabase'] == FALSE)
                    <tr class="table-secondary">
                        <td>{{$list['value']}}</td>
                        <td>Never</td>
                        <td>Never</td>
                        <td>
                            <a class="btn btn-success" href="{{ url('admin/create/' . $list['value']) }}"><i
                                    class="bi bi-plus-circle"></i> Create</a>
                        </td>
                    </tr>
                @else
                    <tr class="table-success">
                        <td>{{$list['value']}}</td>
                        <td>{{$list['DBLastModifiedAt']}}</td>
                        <td>{{$list['XMLLastModified']}}</td>
                        <td>
                            <form action="{{ route('delete_acquisitions_list', $list['value']) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <a class="btn btn-warning"
                                   href="{{ url('admin/edit/' . $list['value']) }}"><i class="bi bi-pencil"></i>
                                    Edit</a>
                                <button type="submit"
                                        onclick="return confirm('Are you sure you want to delete this acquisitions list?')"
                                        class="btn btn-danger" id="{{$list['value']}}-delete"><i
                                        class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endif
            @endif

        @endforeach
        </tbody>
    </table>
    @empty($deletedInAlmaAnalyticsButExistsInDatabase)
    @else
        {{-- Acquisitions Lists no longer in Alma but in our database --}}
        <h2>Acquisitions lists no longer in Alma but in our database</h2>

        <table class="table table-hover" id="acquisition_lists-table">
            <thead>
            <tr>
                <th>Acquisitions list name in the database</th>
                <th>Options</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($deletedInAlmaAnalyticsButExistsInDatabase as $deletedList)
                <tr class="table-danger">
                    <td>{{$deletedList}}</td>
                    <td>
                        <form action="{{ route('delete_acquisitions_list', $deletedList) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    onclick="return confirm('Are you sure you want to delete this acquisitions list?')"
                                    class="btn btn-danger" id="{{$deletedList}}-delete"><i
                                    class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
            </tbody>
        </table>
        @endforeach
    @endempty
@endsection

@section('javascript')
    <script>
        $('#refresh_databases').click(function () {
            $('#refresh_databases').html(
                '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Refreshing databases. This can take up to 5 minutes.'
            ).addClass('disabled')
        });
    </script>
@endsection

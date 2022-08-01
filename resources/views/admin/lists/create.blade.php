@extends('layouts.app')
@section('css')
@endsection

@section('content')
    {{-- SECTION content --}}

    <h2>Create a new acquisitions list</h2>
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
    @isset($XMLFile)
        @if ($XMLFile['XMLFileExists'] == FALSE)
            <div class="mb-3">
                <a class="btn btn-secondary pull-right"
                   href="{{route('import_xml_file', [ 'acquisitionListTitle' => $acquisitionListTitle ])}}"
                   id="import-xml"><i
                        class="bi bi-cloud-download"></i> Import XML</a>
                <div id="xml-import-help" class="form-text">
                    Import the acquisitions list's XML file without adding it to the database.
                </div>
            </div>
        @else
            <div class="mb-3">
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle-fill me-2" aria-label="Success:"></i>
                    <div>
                        An XML file for this acquisitions list is present.
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <a class="btn btn-secondary pull-right"
                   href="{{route('import_xml_file', [ 'acquisitionListTitle' => $acquisitionListTitle ])}}"
                   id="reimport-xml"><i
                        class="bi bi-cloud-download"></i> Reimport XML</a>
                <div id="refresh-acquisition-list-help" class="form-text">
                    Re-download the XML file, without adding it to the database
                </div>
            </div>
        @endif
    @endisset

    <form action="{{ route('create_new_acquisitions_list', [ 'acquisitionListTitle' => $acquisitionListTitle ] )}}"
          method="post" id="create-new-acquisitions-list-form">
        @csrf
        <div class="mb-3">
            <label for="acquisitions_alma_source">Acquisitions</label>
            <input readonly name="acquisitions_alma_source" type="text" class="form-control"
                   id="acquisitions_alma_source" aria-describedby="acquisitions_alma_source"
                   value="{{$acquisitionListTitle}}" required>
            <small id="almaSourceHelp" class="form-text text-muted">The Alma Acquisitions List that will be
                created.</small>
        </div>
        <div class="mb-3">
            <label for="acquisitions_list_name">Acquisitions list name</label>
            <input name="acquisitions_list_name" type="text" class="form-control" id="acquisitions_list_name"
                   aria-describedby="acquisitions_list_name"
                   value="{{ old('acquisitions_list_name') ? old('acquisitions_list_name') : "" }}" required>
            <small id="acquisitions_list_source" class="form-text text-muted">Give a name to your acquisitions
                list.</small>
            <div class="invalid-feedback">
                Please fill in this field.
            </div>
        </div>
        <div class="mb-3">
            <label for="url_path">Acquisitions list URL</label>
            <input name="url_path" type="text" class="form-control" id="url_path"
                   value="{{ old('url_path') ? old('url_path') : "" }}" required>
            <small class="form-text text-muted">This will determine the URL of this acquisitions list:
                https://example.com/what-you-type-here. Shorter URLs will perform better
                on search engines.</small>
            <div class="invalid-feedback">
                Please fill in this field.
            </div>
        </div>
        <button class="btn btn-success" id="create-acquisitions-list" type="submit"><i class="bi bi-plus-circle"></i>
            Create
            acquisitions list
        </button>
        <a class="btn btn-danger pull-right" href="{{route('home')}}" id="cancel-add-acquisitions"><i
                class="bi bi-x-circle"></i> Cancel</a>
    </form>
    <div id="create-acquisitions-list-help" class="form-text">
        Create a new acquisitions list, and import the acquisitions to the database.
    </div>


@endsection

@section('javascript')
    <script>
        (function () {
            'use strict';
            window.addEventListener('load', function () {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function (form) {
                    form.addEventListener('submit', function (event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
    <script>
        $('#import-xml').click(function () {
            $('#import-xml').html(
                '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Importing XML. This can take up to 5 minutes.'
            ).addClass('disabled');
        });
        $('#cancel-add-acquisitions').click(function () {
            $('#cancel-add-acquisitions').html(
                '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Cancel'
            ).addClass('disabled');
        });
        $('#create-acquisitions-list').click(function () {
            $('#create-acquisitions-list').html(
                '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Creating acquisition list. Please wait.'
            ).addClass('disabled');
        });
        $('#reimport-xml').click(function () {
            $('#reimport-xml').html(
                '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Reimporting XML. This can take up to 5 minutes.'
            ).addClass('disabled');
        });
    </script>
@endsection

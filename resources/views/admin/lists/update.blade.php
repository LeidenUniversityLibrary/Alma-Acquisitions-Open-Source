@extends('layouts.app')
@section('css')
@endsection

@section('content')
    <!-- SECTION content -->
    <h4>Update this acquisitions list</h4>
    <form action="{{ route('update_acquisitions_list', [ 'acquisitionListTitle' => $list_data ])}}" method="post"
          enctype="multipart/form-data">
        @csrf
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
                {{ session('status') }}
            </div>
        @endif
        <div class="mb-3">
            <label for="acquisitions_alma_source">Acquisitions list ID</label>
            <input readonly name="acquisitions_alma_source" type="text" class="form-control"
                   id="acquisitions_alma_source"
                   value="{{ $list_data->acquisitions_alma_source }}" required>
            <div class="invalid-feedback">
                Please fill in this field.
            </div>
        </div>
        <div class="mb-3">
            <label for="acquisitions_list_name">Acquisitions list</label>
            <input name="acquisitions_list_name" type="text" class="form-control" id="acquisitions_list_name"
                   value="{{ $list_data->acquisitions_list_name }}" required>
            <div class="invalid-feedback">
                Please fill in this field.
            </div>
        </div>
        <div class="mb-3">
            <label for="url_path">Acquisitions list's URL path</label>
            <small class="text-muted">Editing this path will break old existing links on the internet leading to the
                acquisitions list.</small>
            <input name="url_path" type="text" class="form-control" id="url_path"
                   value="{{ $list_data->url_path }}">
        </div>
        <hr>
        <button class="btn btn-success" type="submit" id="update-acquisitions-list"><i class="bi bi-plus-circle"></i>
            Update
            list
        </button>
        <a class="btn btn-danger pull-right" id="cancel-update-acquisitions" href="{{(route('home'))}}"><i
                class="bi bi-x-circle"></i> Cancel</a>
    </form>
    <hr>
    <div class="mb-3">
        <a class="btn btn-primary pull-right"
           href="{{route('refresh_single_acquisitions_list', [ 'acquisitionListTitle' => $list_data->acquisitions_alma_source ])}}"
           id="refresh-acquisition-list"><i
                class="bi bi-cloud-download"></i> Refresh this acquisitions list</a>
        <div id="refresh-acquisition-list-help" class="form-text">
            Import the latest acquisitions from Alma Analytics for this acquisition list.
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function () {
            'use strict';
            window.addEventListener('load', function () {
                // Fetch all the forms we want to apply custom Bootstrap validation styles to
                var forms = document.getElementsByClassName('needs-validation');
                // Loop over them and prevent submission
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
        $('#refresh-acquisition-list').click(function () {
            $('#refresh-acquisition-list').html(
                '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Refreshing acquisitions list. This can take up to 5 minutes.'
            ).addClass('disabled');
        });
        $('#cancel-update-acquisitions').click(function () {
            $('#cancel-update-acquisitions').html(
                '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Cancel'
            ).addClass('disabled');
        });
        $('#update-acquisitions-list').click(function () {
            $('#update-acquisitions-list').html(
                '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Updating acquisition list. Please wait.'
            ).addClass('disabled');
        });
    </script>
@endsection

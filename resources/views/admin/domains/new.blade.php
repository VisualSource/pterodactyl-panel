@extends('layouts.admin')

@section('title')
    Domains
@endsection

@section('content-header')
    <h1>Domains<small>All currently registered domains.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li><a href="{{ route('admin.domains') }}">Domains</a></li>
        <li class="active">New Domain</li>
    </ol>
@endsection

@section('content')
    <div class="row">
        <form method="POST" action="{{ route('admin.domains.new') }}">
          
            <div class="col-sm-4 col-xs-12">
                <div class="box box-primary">
                    <div class="box-body">
                        <div class="form-group">
                            <label class="control-label" for="memoField">Domain <span class="field-required"></span></label>
                            <input placeholder="example" id="memoField" type="text" name="domain" class="form-control">
                        </div>
                        <p class="text-muted">Once you create domain you will not be able to edit it. The sub domain will be create with 2-5 minutes.</p>
                    </div>
                    <div class="box-footer">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-success btn-sm pull-right">Create Domain</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('footer-scripts')
    @parent
    <script>
    </script>
@endsection

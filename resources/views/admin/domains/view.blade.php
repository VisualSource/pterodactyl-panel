@extends('layouts.admin')

@section('title')
    Domain - {{ str_limit($domain->domain, 50) }}
@endsection

@section('content-header')
    <h1>Domain<small>{{ str_limit($domain->domain, 50) }}</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li><a href="{{ route('admin.domains') }}">Domains</a></li>
        <li class="active">{{ $domain->domain }}</li>
    </ol>
@endsection

@section('content')
<div class="row">
    
</div>
@endsection

@section('footer-scripts')
    @parent
@endsection

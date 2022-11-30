{{-- Pterodactyl - Panel --}}
{{-- Copyright (c) 2022 - 2023 Collin Blosser <cblosser@titanhosting.us> --}}

{{-- This software is licensed under the terms of the MIT license. --}}
{{-- https://opensource.org/licenses/MIT --}}

@extends('layouts.admin')

@section('title')
    Domains
@endsection

@section('content-header')
    <h1>Domains<small>All currently registered domains.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li class="active">Domains</li>
    </ol>
@endsection

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Configured Domains</h3>
                <div class="box-tools">
                    <a href="{{ route('admin.nests.new') }}" class="btn btn-primary btn-sm">Create New</a>
                </div>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <tr>
                        <th>ID</th>
                        <th>FQDN</th>
                        <th>Server</th>
                    </tr>
                    @foreach($domains as $domain)
                        <tr>
                            <td class="middle"><code>{{ $domain->id }}</code></td>
                            <td class="col-xs-6 middle">{{ $domain->domain }}</td>
                            @if(!is_null($domain->server_id)) 
                                <td class="middle"><a href="{{ route('admin.servers.view', $domain->id) }}" data-toggle="tooltip" data-placement="right" title="Server ID">{{ $domain->server_id }}</a></td>
                            @else 
                                <td class="middle">Not Assigned</td>
                            @endif
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer-scripts')
    @parent
@endsection
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
                    <a href="{{ route('admin.domains.new') }}" class="btn btn-primary btn-sm">Create New</a>
                </div>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <tr>
                        <th>ID</th>
                        <th>FQDN</th>
                        <th>Sub Domain</th>
                        <th>Server</th>
                        <th>Created</th>
                        <th></th>
                    </tr>
                    @foreach($domains as $domain)
                        <tr>
                            <td class="middle"><code>{{ $domain->id }}</code></td>
                            <td>{{ $domain->domain }}.titanhosting.us</td>
                            <td><a href="{{ route('admin.domains.view', $domain->id) }}">{{ $domain->domain }}</a></td>
                            @if(!is_null($domain->server_id)) 
                                <td><a href="{{ route('admin.servers.view', $domain->id) }}" data-toggle="tooltip" data-placement="right" title="Server ID">{{ $domain->server_id }}</a></td>
                            @else 
                                <td>Not Assigned</td>
                            @endif
                            <td>{{ $domain->created_at }}</td>
                            <td>
                                <a href="#" data-action="del-domain" data-attr="{{ $domain->id }}">
                                    <i class="fa fa-trash-o text-danger"></i>
                                </a>
                            </td>
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
    <script>
        $(document).ready(function(){
            $('[data-action="del-domain"]').click(function(event){
                const self = $(this);
                event.preventDefault();
                swal({
                    type: "error",
                    title: "Removing Domain",
                    text: "Are you sure that you want to do this?",
                    showCancelButton: true,
                    allowOutsideClick: true,
                    closeOnConfirm: false,
                    confirmButtonText: 'Remove',
                    confirmButtonColor: '#d9534f',
                    showLoaderOnConfirm: true
                },()=>{
                    $.ajax({
                        method: "DELETE",
                        url: '/admin/api/domains/' + self.data("attr"),
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).done(()=>{
                        swal({
                            type: "success",
                            title: '',
                            text: 'The process for removing the domain has started. Wait 2-5 minutes to see changes.'
                        })
                        self.parent().parent().slideUp();
                    }).fail((err)=>{
                        console.error(err);
                        swal({
                            type: "Error",
                            title: "Whoops!",
                            text: "An error occurred while attempting to remove this domain."
                        })
                    });
                });
            });
        });
    </script>
@endsection
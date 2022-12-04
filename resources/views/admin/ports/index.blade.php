{{-- Pterodactyl - Panel --}}
{{-- Copyright (c) 2022 - 2023 Collin Blosser <cblosser@titanhosting.us> --}}

{{-- This software is licensed under the terms of the MIT license. --}}
{{-- https://opensource.org/licenses/MIT --}}

@extends('layouts.admin')

@section('title')
    Ports
@endsection

@section('content-header')
    <h1>Ports<small>All currently open ports.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li class="active">Ports</li>
    </ol>
@endsection

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Configured Ports</h3>
                <div class="box-tools">
                    <a href="{{ route('admin.ports.new') }}" class="btn btn-primary btn-sm">Create New</a>
                </div>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <tr>
                        <th>ID</th>
                        <th>External Port</th>
                        <th>Description</th>
                        <th>Allocation</th>
                        <th>Created</th>
                        <th></th>
                    </tr>
                    @foreach($ports as $port)
                        <tr>
                            <td class="middle"><code>{{ $port->id }}</code></td>
                            <td><a href="{{ route('admin.ports.view', $port->id) }}">{{ $port->external_port }}</a></td>
                            <td>{{ $port->description }}</td>
                            <td><a href="{{ route('admin.nodes.view.allocation',$port->allocation->node_id) }}">{{ $port->allocation->ip }}:{{ $port->allocation->port }}</a></td>
                            <td>{{ $port->created_at }}</td>
                            <td>
                                <a href="#" data-action="del-port" data-attr="{{ $port->id }}">
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
            $('[data-action="del-port"]').click(function(event){
                const self = $(this);
                event.preventDefault();
                swal({
                    type: "error",
                    title: "Removing Port",
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
                        url: '/admin/ports/' + self.data("attr"),
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).done(()=>{
                        swal({
                            type: "success",
                            title: '',
                            text: 'The process for removing the port has started. Wait 2-5 minutes to see changes.'
                        })
                        self.parent().parent().slideUp();
                    }).fail((err)=>{
                        console.error(err);
                        swal({
                            type: "error",
                            title: "Whoops!",
                            text: "An error occurred while attempting to remove this port.",
                            confirmButtonText: 'Ok',
                            confirmButtonColor: '#d9534f',
                            showLoaderOnConfirm: false,
                            showCancelButton: false,
                            closeOnConfirm: true,
                        })
                    });
                });
            });
        });
    </script>
@endsection
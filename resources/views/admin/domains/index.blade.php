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
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#newDomainModal">Create New</button>
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
                            <td><a href="#" data-action="patch-domain"  id="domain-{{$domain->id}}" data-toggle="modal" data-target="#patchDomainModal" data-attr="{{ json_encode(['id'=>$domain->id,'domain'=>$domain->domain,'server_id'=>$domain->server_id]) }}">{{ $domain->domain }}</a></td>
                            @if(!is_null($domain->server_id)) 
                                <td><a href="{{ route('admin.servers.view', $domain->server->id) }}" data-toggle="tooltip" data-placement="right" title="Server ID">{{ $domain->server->name }}</a></td>
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
<div class="modal fade" id="patchDomainModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="patchform">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Updade Domain</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input type="number" name="id" class="hidden" value="" />
                        <div class="col-md-12">
                            <label for="pShortModal" class="form-label">Sub Domain</label>
                            <input readonly type="text" name="domain" id="pShortModal" class="form-control" />
                            <p class="text-muted small">A short identifier used to distinguish this sub domain from others. Must be between 3 and 60 characters, for example, <code>docs</code>.</p>
                        </div>
                        <div class="col-md-12">
                            <label for="pServerModal" class="form-label">Assigned Server</label>
                            <input type="text" name="server_id" id="pServerModal" class="form-control" />
                            <p class="text-muted small">Server Id number.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm pull-left" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="newDomainModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.domains.new') }}" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Create Domain</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="pShortModal" class="form-label">Sub Domain</label>
                            <input type="text" name="domain" id="pShortModal" class="form-control" />
                            <p class="text-muted small">A short identifier used to distinguish this sub domain from others. Must be between 3 and 60 characters, for example, <code>docs</code>.</p>
                        </div>
                        <div class="col-md-12">
                            <label for="pServerModal" class="form-label">Assigned Server</label>
                            <input type="text" name="server_id" id="pServerModal" class="form-control" />
                            <p class="text-muted small">Server Id number.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {!! csrf_field() !!}
                    <button type="button" class="btn btn-default btn-sm pull-left" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('footer-scripts')
    @parent
    <script>
        $(document).ready(function(){
            $("#patchform").on("submit",(ev)=>{
                ev.preventDefault();
                $("#patchDomainModal").modal("toggle");
                const data = new FormData(ev.target);
                data.delete("domain");
                $.ajax({
                    url: "/admin/domains/" + data.get("id"),
                    method: "PATCH",
                    data,
                    processData: false,
                    contentType: false,
                    headers: {
                        "Content-Type": "multipart/form-data",
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).always(()=>window.location.reload());
            });
            $('[data-action="patch-domain"]').click(function(ev){
                const data = JSON.parse(ev.target.getAttribute("data-attr") || "null");
                $('#patchform > .modal-body > .row > .col-md-12 > [name="domain"]').attr("value",data.domain);
                $('#patchform > .modal-body > .row > [name="id"]').attr("value",data.id);
                $('#patchform > .modal-body > .row > .col-md-12 > [name="server_id"]').attr("value",data.server_id);
            });
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
                        url: '/admin/domains/' + self.data("attr"),
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
                            type: "error",
                            title: "Whoops!",
                            text: "An error occurred while attempting to remove this domain.",
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
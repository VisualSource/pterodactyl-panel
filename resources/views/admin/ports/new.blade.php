@extends('layouts.admin')

@section('title')
    New Port
@endsection

@section('content-header')
    <h1>Open Port<small>Open a new port.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li><a href="{{ route('admin.ports') }}">Ports</a></li>
        <li class="active">Open Port</li>
    </ol>
@endsection

@section('content')
<form action="{{ route('admin.ports.create') }}" method="POST">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Core Details</h3>
                </div>

                <div class="box-body row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pExternalPort">External Port</label>
                            <input type="text" class="form-control" id="pExternalPort" name="external_port" placeholder="External Port">
                            <p class="small text-muted no-margin">The external port that will be used to connect services to.</p>
                        </div>

                        <div class="form-group">
                            <label for="pNType">Network Type</label>
                            <select id="pNType" name="type" class="form-control">
                                <option value="both">Both</option>
                                <option value="tcp">TCP</option>
                                <option value="udp">UDP</option>
                            </select>
                            <p class="small text-muted no-margin">Network type.</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pAllocation">Linked Allocation</label>
                            <select id="pAllocation" name="allocation_id" class="form-control">
                                @foreach($allocations as $allocation)
                                    <option value="{{$allocation['id']}}">{{ $allocation['node'] }} | {{ $allocation['address'] }}</option>
                                @endforeach
                            </select>
                            <p class="small text-muted no-margin">Allocaiton</p>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    {!! csrf_field() !!}
                    <input type="submit" class="btn btn-success pull-right" value="Open Port" />
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="overlay" id="allocationLoader" style="display:none;"><i class="fa fa-refresh fa-spin"></i></div>
                <div class="box-header with-border">
                    <h3 class="box-title">Port Options</h3>
                </div>

                <div class="box-body row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pInteralPort">Internal Port</label>
                            <input type="text" class="form-control" id="pInternalPort" name="internal_port" placeholder="Internal Port">
                            <p class="small text-muted no-margin">The internal port that will be used to connect services to.  Uses external port by default.</p>
                        </div>
                        <div class="form-group">
                            <label for="pMethod">Protocol</label>
                            <select id="pMethod" name="method" class="form-control">
                                <option value="upnp">Universal Plug and Play (Upnp)</option>
                                <option value="pmp">Port Mapping Protocol (PMP)</option>
                            </select>
                            <p class="small text-muted no-margin">The protocol that is used to open, close, and test ports.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pDescription" class="control-label">Port Description</label>
                            <input type="text" id="pDescription" name="description" class="form-control"/>
                            <p class="text-muted small">A brief description of this port.</p>
                        </div>

                        <div class="form-group">
                            <label for="pIA">Internal Address</label>
                            <input type="text" class="form-control" id="pIA" name="internal_address" placeholder="Internal Address">
                            <p class="small text-muted no-margin">The internal address to map this port to. Default uses current system's address.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
</from>
@endsection

@section('footer-scripts')
    @parent
@endsection

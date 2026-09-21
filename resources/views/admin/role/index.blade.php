@extends('layouts.master')

@section('title','User list')
@section('page_title','User list')

@section('contents')

<div class="container-fluid">
<div class="row">
    <div class="col-12">
    <div class="card">
        <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                    style="background: linear-gradient(90deg, #343a40, #212529);">
        <h3 class="card-title">Responsive Hover Table</h3>

        <div class="card-tools">
            <div class="input-group input-group-sm">
                <div class="input-group-append">
                    <button type="button" class="btn btn-primary p-1 rounded-sm">
                    <i class="fa fa-plus"></i> Add More
                    </button>
                </div>
            </div>
        </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Date</th>
                <th>Status</th>
                <th>Reason</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>183</td>
                <td>John Doe</td>
                <td>11-7-2014</td>
                <td><span class="tag tag-success">Approved</span></td>
                <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
            </tr>
            
            </tbody>
        </table>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
    </div>
</div>
</div>

@endsection
@extends('layouts.master')

@section('contents')
    <section class="content">
        <div class="container-fluid mt-4">
            <div class="card">
                <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                    style="background: linear-gradient(90deg, #343a40, #212529);">
                    <h3 class="card-title">User Information Form</h3>
                </div>
                <div class="card-body">
                    <form>
                        <div class="row">
                            <!-- Reference ID Dropdown -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Reference ID (Dropdown)</label>
                                    <select class="form-control">
                                        <option value="">Select ID</option>
                                        <option value="1">ID 1</option>
                                        <option value="2">ID 2</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Reference ID Text Field -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Reference ID (Text Field)</label>
                                    <input type="text" class="form-control" placeholder="Enter Reference ID">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- NID/BC/Passport -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>NID/BC/Passport</label>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="nid" name="doc_type" class="custom-control-input">
                                        <label class="custom-control-label" for="nid">NID</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="bc" name="doc_type" class="custom-control-input">
                                        <label class="custom-control-label" for="bc">Birth Certificate</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="passport" name="doc_type" class="custom-control-input">
                                        <label class="custom-control-label" for="passport">Passport</label>
                                    </div>
                                    <input type="file" class="form-control mt-2">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Father Name -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Father's Name</label>
                                    <input type="text" class="form-control" placeholder="Enter Father's Name">
                                </div>
                            </div>
                            <!-- Mother Name -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mother's Name</label>
                                    <input type="text" class="form-control" placeholder="Enter Mother's Name">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Date of Birth -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date of Birth</label>
                                    <input type="date" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Permanent Address -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Permanent Address</label>
                                    <input type="text" class="form-control" placeholder="Enter Permanent Address">
                                </div>
                            </div>
                            <!-- Present Address -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Present Address</label>
                                    <input type="text" class="form-control" placeholder="Enter Present Address">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

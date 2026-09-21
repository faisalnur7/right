<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $data['title'] = "Role";
        return view('admin.role.index', $data);
    }
}

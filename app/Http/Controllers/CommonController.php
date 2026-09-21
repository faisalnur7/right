<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Division;
use App\Models\PoliceStation;
use App\Models\PostOffice;
use App\Models\User;
use App\Models\BinaryTreeNode;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CommonController extends Controller
{

    public function load_affiliate_id(Request $request){
        $user = User::findOrFail($request->ref_user_id);
        $refered_user_count = User::query()->where('reference_user_id',$user->id)->count();
        $reference_id = 'RB-'.sprintf('%04.3d', $user->id).'-'.sprintf('%04.3d', $refered_user_count+1);
        return ['reference_id' => $reference_id];
    }

    public function load_districts(Request $request)
    {
        $data['districts'] = District::query()->where('division_id',$request->division_id)->get();
        return $data;
    }

    public function load_police_stations(Request $request)
    {
        $data['police_stations'] = PoliceStation::query()->where('district_id',$request->district_id)->get();
        return $data;
    }

    public function load_post_offices(Request $request)
    {
        $data['post_offices'] = PostOffice::query()->where('police_station_id',$request->police_station_id)->get();
        return $data;
    }

    public function get_user_sale_info(Request $request){
        $user = User::find($request->user_id);
        $refUser = $user->referenceUser;

        return BinaryTreeNode::query()->where('user_id', $refUser->id)->where('sale_log_id', $request->sale_log_id)->where('status', 1)->exists();
    }

    public function unread_notifications(){
        $userId = auth()->id();

        $notifications = Notification::with('user')->where('is_for_admin', 1)
                            ->latest()
                            ->get();

        $html = view('layouts.partials._admin_notifications_view', compact('notifications'))->render();
        $unreadCount = Notification::where('is_for_admin', 1)->where('is_read_by_admin', 0)->count();

        return response()->json([
            'unreadCount' => $unreadCount,
            'html' => $html
        ]);
    }

    public function unread_notifications_user(){
        $userId = auth()->id();

        $notifications = Notification::with('user')
                  ->where(function ($q) {
                      $q->where('user_id', auth()->id())->orWhereNull('user_id');
                  })
                  ->where('is_for_admin', 0)
                  ->latest()
                  ->get();

        $unreadCount = Notification::with('user')
                  ->where(function ($q) {
                      $q->where('user_id', auth()->id())->orWhereNull('user_id');
                  })
                  ->where('is_for_admin', 0)
                  ->where('is_read', 0)
                  ->count();

        $html = view('layouts.partials._notifications_view', compact('notifications'))->render();

        return response()->json([
            'unreadCount' => $unreadCount,
            'html' => $html
        ]);
    }

}
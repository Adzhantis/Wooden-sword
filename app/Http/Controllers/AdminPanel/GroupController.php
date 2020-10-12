<?php

namespace App\Http\Controllers\AdminPanel;

use App\AutoGroupState;
use App\Http\Controllers\Controller;
use App\Services\AutoGroupService;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    public function index()
    {
        $groups = DB::table('groups')->orderBy('id', 'desc')->get();
        $groupLabels = DB::table('groups')->pluck('label', 'id')->toArray();

        $autoGroupsState = (new AutoGroupState())->get();

        return view('group.index', compact(
            'groups',
            'autoGroupsState',
            'groupLabels'
        ));
    }

    public function resetCounters()
    {
        (new AutoGroupState())->reset();
        return redirect('adminpanel/groups')->with('status', 'Counters are successfully reset!');
    }
}


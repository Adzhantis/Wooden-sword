<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GroupController extends Controller
{
    public function index()
    {
        if (!Storage::disk('local')->exists('autoGroupsState.txt')) {
            return redirect('adminpanel/groups/reset-counters');
        }

        $groups = DB::table('groups')->orderBy('id', 'desc')->get();
        $groupLabels = DB::table('groups')->pluck('label', 'id')->toArray();

        $autoGroupsStateText = Storage::disk('local')->get('autoGroupsState.txt');
        $autoGroupsState = json_decode($autoGroupsStateText);

        //dd($autoGroupsState, $autoGroupsStateText, $groupLabels);

        return view('group.index', compact(
            'groups',
            'autoGroupsState',
            'groupLabels'
        ));
    }

    public function resetCounters()
    {
        $weight    = '1,2,7';
        $weightArr = explode(',', $weight);
        $weightSum = array_sum($weightArr);

        $values = [];
        for ($i=0; $i < $weightSum; $i++) {
            $values[] = $i;
        }

        $groups = DB::table('groups')->limit(count($weightArr))->get()->toArray();
        $stateData = [];
        foreach ($weightArr as $weightSingle) {
            $group = array_shift($groups);
            $stateData['autoGroups'][$group->id] = [
                'weight'             => $weightSingle,
                'weightPercent'      => $weightSingle * $weightSum,
                'countPlayer'        => 0,
                'countPlayerPercent' => 0,
            ];
        }

        $stateData['total'] = [
            'weight'      => $weightSum,
            'countPlayer' => 0,
        ];


        Storage::disk('local')->put('autoGroupsState.txt', json_encode($stateData));

        return redirect('adminpanel/groups')->with('status', 'Counters are successfully reset!');
    }
}


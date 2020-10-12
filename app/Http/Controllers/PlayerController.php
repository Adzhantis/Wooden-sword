<?php

namespace App\Http\Controllers;

use App\AutoGroupState;
use App\Player;
use App\Services\AutoGroupService;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function create(Request $request)
    {
        $displayName = $request->get('display_name');
        $auto        = $request->get('auto');

        if ($auto) {
            $displayName = 'Player #';
        }

        if (!$displayName) {
            return response()->json([
                'result'  => 'error',
                'code'    => 422,
                'message' => 'Field display_name is required'
            ]);
        }

        $player = new Player;
        $chosenGroupId = (new AutoGroupState())->generateIdGroup();

        $player->display_name = $displayName;
        $player->id_group     = $chosenGroupId;
        $player->save();

        if ($auto) {
            $player->display_name .= $player->id;
            $player->update();
        }

        return response()->json([
            'result'  => 'ok',
            'player' => [
                'id'    => $player->id,
                'group' => $player->group->label ?? ''
            ]
        ]);
    }
}


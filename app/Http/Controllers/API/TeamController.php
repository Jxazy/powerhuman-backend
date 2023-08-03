<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTeamRequest;
use App\Http\Requests\UpdateTeamRequest;

class TeamController extends Controller
{

    public function fetch(Request $request)
    {
        // Parameter
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);
        $teamQuery = Team::query();

        // Get single data
        if ($id) {
            $team = $teamQuery->find($id);

            if ($team) {
                return ResponseFormatter::success(
                    $team,
                    'Team found'
                );
            }
            return ResponseFormatter::error(
                'Team not found',
                404
            );
        }

        // Get multiple data
        $teams = $teamQuery->where('company_id', $request->company_id);

        if ($name) {
            $teams->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $teams->paginate($limit),
            'Teams found'
        );
    }

    public function create(CreateTeamRequest $request)
    {
        try {

            // Upload Icons
            if ($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/icons');
            } else {
                $path = null;
            }

            // Create Team
            $team = Team::create([
                'name' => $request->input('name'),
                'icon' => $path,
                'company_id' => $request->company_id,
            ]);

            if (!$team) {
                throw new Exception('Failed to create team');
            }

            return ResponseFormatter::success(
                $team,
                'Team created'
            );
        } catch (\Exception $th) {
            return ResponseFormatter::error(
                $th->getMessage(),
                500
            );
        }
    }

    public function update(UpdateTeamRequest $request, $id)
    {
        try {

            // Get Team
            $team = Team::find($id);

            // check if team exists
            if (!$team) {
                throw new Exception('Team not found');
            }

            // Upload Icons
            if ($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/icons');
            } else {
                $path = null;
            }

            // Update Team
            $team->update([
                'name' => $request->input('name'),
                'icon' => isset($path) ? $path : $team->icon,
                'company_id' => $request->company_id,
            ]);

            return ResponseFormatter::success(
                $team,
                'Team updated'
            );
        } catch (\Exception $th) {
            return ResponseFormatter::error(
                $th->getMessage(),
                500
            );
        }
    }

    public function destroy($id)
    {
        try {

            // Get Team
            $team = Team::find($id);

            // check if team owned by user
            if ($team->company_id != request()->company_id) {
                throw new Exception('Team not found');
            }

            // check if team exists
            if (!$team) {
                throw new Exception('Team not found');
            }

            // Delete Team
            $team->delete();

            return ResponseFormatter::success(
                null,
                'Team deleted'
            );
        } catch (\Exception $th) {
            return ResponseFormatter::error(
                $th->getMessage(),
                500
            );
        }
    }
}

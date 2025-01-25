<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Retrieve all activity logs
        $activityLogs = DB::table('activity_logs')->get();

        return response()->json([
            'success' => true,
            'data' => $activityLogs
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'ref_id' => 'required|integer',
            'ref_table' => 'required|string',
            'title' => 'required|string',
            'short_description' => 'required|string',
            'description' => 'required|string',
            'status_code' => 'required|integer',
            'created_by' => 'required|integer|exists:users,id',
            'updated_by' => 'required|integer|exists:users,id'
        ]);

        // Insert into activity_logs table
        $id = DB::table('activity_logs')->insertGetId([
            'ref_id' => $request->ref_id,
            'ref_table' => $request->ref_table,
            'title' => $request->title,
            'short_description' => $request->short_description,
            'description' => $request->description,
            'status_code' => $request->status_code,
            'created_by' => $request->created_by,
            'updated_by' => $request->updated_by,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Activity log created successfully.',
            'data' => ['id' => $id]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Retrieve specific activity log
        $activityLog = DB::table('activity_logs')->find($id);

        if (!$activityLog) { // If the activity log is not found
            return response()->json([
                'success' => false,
                'message' => 'Activity log not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $activityLog
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate input
        $request->validate([
            'ref_id' => 'sometimes|required|integer',
            'ref_table' => 'sometimes|required|string',
            'title' => 'sometimes|required|string',
            'short_description' => 'sometimes|required|string',
            'description' => 'sometimes|required|string',
            'status_code' => 'sometimes|required|integer',
            'updated_by' => 'required|integer|exists:users,id'
        ]);

        // Check if the activity log exists
        $activityLog = DB::table('activity_logs')->find($id);

        if (!$activityLog) { // If the activity log is not found
            return response()->json([
                'success' => false,
                'message' => 'Activity log not found.'
            ], 404);
        }

        // Update activity log
        DB::table('activity_logs')
            ->where('id', $id)
            ->update(array_merge(
                $request->only([
                    'ref_id',
                    'ref_table',
                    'title',
                    'short_description',
                    'description',
                    'status_code'
                ]),
                [
                    'updated_by' => $request->updated_by,
                    'updated_at' => now()
                ]
            ));

        return response()->json([
            'success' => true,
            'message' => 'Activity log updated successfully.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Check if the activity log exists
        $activityLog = DB::table('activity_logs')->find($id);

        if (!$activityLog) { // If the activity log is not found
            return response()->json([
                'success' => false,
                'message' => 'Activity log not found.'
            ], 404);
        }

        // Delete activity log
        DB::table('activity_logs')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Activity log deleted successfully.'
        ]);
    }
}

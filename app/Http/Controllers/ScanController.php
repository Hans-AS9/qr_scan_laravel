<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use App\Models\Attendance;
use App\Models\Participant;
use Dompdf\Css\Content\Attr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ScanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Scan::get();

        return response()->json([
            "status" => "success",
            "message" => "oke",
            "data" => $data
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //validation
        $validator = Validator::make(
            $request->all(),
            [
                'title' => 'required',
            ]

        );
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => "validation error",
                'errors' => $validator->errors(),
                'data' => []
            ]);
        }

        $scan = new Scan();
        $scan->title = $request->title;

        $result = $scan->save();

        if ($result) {
            return response()->json([
                "status" => "success",
                "message" => "Save data succes",
                "data" => []
            ], 200);
        } else {
            return response()->json([
                "status" => "error",
                "message" => "Save data failed",
                "data" => []
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Scan::find($id);
        return response()->json([
            "status" => "success",
            "message" => "OKe",
            "data" => $data
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //

        $scan = Scan::find($id);

        if ($scan == null) {
            return response()->json([
                "status" => "error",
                "message" => "Scan not found",
                "data" => []
            ], 404);
        }

        $scan->title = $request->title;

        $result = $scan->save();

        if ($result) {
            return response()->json([
                "status" => "success",
                "message" => "Update data succes",
                "data" => []
            ], 200);
        } else {
            return response()->json([
                "status" => "error",
                "message" => "Update data failed",
                "data" => []
            ], 400); // Bad Request
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $scan = Scan::find($id);
        if ($scan == null) {
            return response()->json([
                "status" => "error",
                "message" => "Scan not found",
                "data" => []
            ], 404);
        }

        $result = $scan->delete();

        if ($result) {
            return response()->json([
                "status" => "success",
                "message" => "Delete data succes",
                "data" => []
            ], 200);
        } else {
            return response()->json([
                "status" => "error",
                "message" => "Delete data failed",
                "data" => []
            ], 400); // Bad Request
        }
    }

    public function scan_qr(Request $request)
    {

        $request->validate([
            'id_scan' => 'required',
            'qr_content' => 'required',
        ]);

        $user = Auth::user();

        $is_id_scan = Scan::where("id", $request->id_scan)->first();

        if (!$is_id_scan) {
            return response()->json([
                "status" => "failed",
                "message" => "id scan not found",
                "errors" => [
                    "id_scan" => "Not Found",
                ]

            ], 400);
        }

        $is_participant = Participant::where("qr_content", $request->qr_content)->first();

        if (!$is_participant) {
            return response()->json([
                "status" => "failed",
                "message" => "Participant not found",
                "errors" => [
                    "qr_content" => "Not Found"
                ]
            ], 404);
        }

        $today = now()->startOfDay();
        $alreadyScan = Attendance::where("participant_id", $is_participant->id)
            ->where("id_scan", $is_id_scan->id)
            ->whereDate("scan_at", $today)
            ->first();

        if ($alreadyScan) {
            return response()->json([
                "status" => "OK",
                "message" => "Anda sudah scan hari ini!",
            ], 200);
        }

        $Attendance = new Attendance();
        $Attendance->participant_id = $is_participant->id;
        $Attendance->id_scan = $is_id_scan->id;
        $Attendance->scan_at = now();
        $Attendance->scan_by = $user->id;

        $Attendance->save();

        if ($Attendance) {
            return response()->json([
                "status" => "success",
                "message" => $is_id_scan->title . " - " . $request->qr_scan . "Successfully",
            ], 200);
        } else {
            return response()->json([
                "status" => "failed",
                "message" => "error when saving data",
            ], 500);
        }
    }
}

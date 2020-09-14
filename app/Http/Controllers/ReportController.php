<?php

namespace App\Http\Controllers;

use App\ReportPost;
use App\ReportUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{

    /**
     * Protecting ReportController
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Reporting user
     * 
     * @param \Illuminate\Http\Request $request 
     * 
     * @return \Illuminate\Http\Respons
     */
    public function reportUser(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'from' => 'required',
            'to' => 'required',
            'reason' => 'required',
            'description' => 'required|string'
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => 'failed',
                'success' => false,
                'msg' => 'validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $report = ReportUser::create([
            'from' => auth()->id(),
            'to' => $request->input('to'),
            'reason' => $request->input('reason'),
            'description' => $request->input('description')
        ]);

        if ($report) {

            return response()->json([
                'status' => 200,
                'success' => true,
                'msg' => 'This user has been reported',
                'report' => $report,
                'view_report' => [
                    'href' => 'admin/user/report',
                    'method' => 'GET'
                ]
                ], 200);
        }

        return response()->json([
            'staus' => 404,
            'success' => false,
            'msg' => 'Error during reporting'
        ]);
    }

    /**
     * Reporting post
     * 
     * @param \Illuminate\Http\Request $request 
     * 
     * @return \Illuminate\Http\Respons
     */
    public function reportPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from' => 'required',
            'post_id' => 'required',
            'reason' => 'required|string',
            'description' => 'required'
        ]);

        if ($validator->fails()) {
            
            return response()->json([
                'status' => 'failed',
                'success' => false,
                'msg' => 'validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $report = ReportPost::create([
            'from' => auth()->id(),
            'post_id' => $request->post_id,
            'reason' => $request->reason,
            'description' => $request->description
        ]);

        if ($report) {
            
            return response()->json([
                'status' => 200,
                'success' => true,
                'msg' => 'This post has been reported',
                'report' => $report,
                'view_report' => [
                    'href' => 'admin/post/report'
                ]
            ]);
        }

        return response()->json([
            'staus' => 404,
            'success' => false,
            'msg' => 'Error during reporting'
        ]);
    }
}

<?php

namespace App\Http\Controllers\API\Version1;

use App\Http\Controllers\Controller;
use App\Models\TrackHistory;
use Illuminate\Http\Request;

class TrackHistoryAPIController extends Controller
{
    //
    /**
     * @OA\Get(
     *     path="/api/v1/track_histories",
     *     summary="Get all Track Histories",
     *     tags={"Track History"},
     *     @OA\Response(response=200, description="Successful response")
     * )
     */
    public function index()
    {
        try {
            $track_histories = TrackHistory::all();
            return response()->json($track_histories);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/v1/track_history_register",
     *     summary="Store a new Track History",
     *     tags={"Track History"},
     *
     *     @OA\RequestBody(

     *         @OA\JsonContent(
     * @OA\Property(property="track_id", type="string", example=""),
     * @OA\Property(property="order_id", type="string", example=""),
     *             @OA\Property(property="title", type="string", example=""),
     *             @OA\Property(property="staff_name", type="string", example=""),
     *             @OA\Property(property="role", type="string", example=""),
     *             @OA\Property(property="status", type="string", example="pending"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Track History created successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */
    public function store(Request $request){
        try{
            $track_history= new TrackHistory();
            $track_history->track_id=$request->track_id;
            $track_history->order_id=$request->order_id;

            $track_history->title=$request->title;
            $track_history->staff_name=$request->staff_name;
            $track_history->role=$request->role;
            $track_history->status=$request->status;
            $track_history->save();

            return response()->json([
                'message' => 'Track History created successfully',
                'track_history' => $track_history,

            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }
     /**
     * @OA\Get(
     *     path="/api/v1/track_history_show/{id}",
     *     summary="Retrieve a specific Track History by ID",
     *     tags={"Track History"},
     *     @OA\Parameter(
     *         description="ID of Track History",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(response=200, description="Track History retrieved successfully"),
     * )
     * */
    public function show($id){
        try {
            $track_history = TrackHistory::find($id);
            if (!$track_history) {
                return response()->json([
                    'message' => 'Track History not found',
                ], 404);
            }
            return response()->json([
                'message' => 'Track History retrieved successfully',
                'track_history' => $track_history,
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Track History retrieve failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
    /**
     * @OA\Delete(
     *     path="/api/v1/track_history_destroy/{id}",
     *     summary="Delete a Track History ",
     *     tags={"Track History"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Track History to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Track History deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Track History not found"
     *     )
     * )
     */
    public function destroy($id){
        try {
            $track_history = TrackHistory::find($id);
            if (!$track_history) {
                return response()->json([
                    'message' => 'Track History not found',
                ], 404);
            }
            $track_history->delete();
            return response()->json([
                'message' => 'Track History deleted successfully',
                'id' => $id,
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Track History delete failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
    /**
     * @OA\Put(
     *     path="/api/v1/track_history_update/{id}",
     *     summary="Update a Track History",
     *     tags={"Track History"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Track History to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="track_id", type="string", example=""),
     *             @OA\Property(property="order_id", type="string", example=""),
     *             @OA\Property(property="title", type="string", example=""),
     *             @OA\Property(property="staff_name", type="string", example=""),
     *             @OA\Property(property="role", type="string", example=""),
     *             @OA\Property(property="status", type="string", example="pending"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Track History updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Track History not found"
     *     )
     * )
     */
    public function update(Request $request, $id){
        try {
            $track_history = TrackHistory::find($id);
            if (!$track_history) {
                return response()->json([
                    'message' => 'Track History not found',
                ], 404);
            }
            $track_history->track_id=$request->track_id;
            $track_history->order_id=$request->order_id;

            $track_history->title=$request->title;
            $track_history->staff_name=$request->staff_name;
            $track_history->role=$request->role;
            $track_history->status=$request->status;
            $track_history->save();
            return response()->json([
                'message' => 'Track History updated successfully',
                'track_history' => $track_history,
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Track History update failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

}

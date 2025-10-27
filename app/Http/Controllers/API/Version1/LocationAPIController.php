<?php

namespace App\Http\Controllers\API\Version1;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Warehouse;

class LocationAPIController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/v1/location",
     *     summary="Retrieve a list of all locations",
     *     tags={"Location"},
     *     @OA\Response(
     *         response=200,
     *         description="Location list retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Location list not found"
     *     )
     * )
     */

    public function index()
    {
        $locations = Warehouse::all();
        return response()->json([
            'message' => 'Locations retrieved successfully',
            'locations' => $locations,
        ], 200);
    }


    /**
     * @OA\Post(
     *     path="/api/v1/location_register",
     *     summary="Store a new location",
     *     tags={"Location"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example=""),
     *             @OA\Property(property="phone", type="string", example=""),
     *             @OA\Property(property="address", type="string", example=""),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Location created successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $location = new Warehouse();
        $location->name = $request->name;
        $location->phone_number = $request->phone??'';
        $location->address = $request->address??'';
        $location->save();

        return response()->json([
            'message' => 'Location registered successfully',
            'location' => $location,
        ], 201);
    }

    /**
     * @OA\Put(
        *     path="/api/v1/location_update/{id}",
        *     summary="Update a location",
        *     tags={"Location"},
        *     @OA\Parameter(
        *         name="id",
        *         in="path",
        *         required=true,
        *         description="ID of the location to update",
        *         @OA\Schema(type="integer")
        *     ),
        *     @OA\RequestBody(
        *         required=true,
        *         @OA\JsonContent(
        *             required={"name"},
        *             @OA\Property(property="name", type="string", example=""),
        *             @OA\Property(property="phone", type="string", example=""),
        *             @OA\Property(property="address", type="string", example=""),
        *         )
        *     ),
        *     @OA\Response(
        *         response=200,
        *         description="Location updated successfully"
        *     ),
        *     @OA\Response(
        *         response=404,
        *         description="Location not found"
        *     )
        * )
        */

    public function update(Request $request, $id)
    {
        $location = Warehouse::find($id);
        if(!$location) {
            return response()->json(['message' => 'Location not found'], 404);
        }
        $location->name = $request->name;
        $location->phone_number = $request->phone??'';
        $location->address = $request->address??'';
        $location->save();


        return response()->json(['message' => 'Location updated successfully', 'location' => $location]);
    }
    /**
     * @OA\Delete(
     *     path="/api/v1/location_destroy/{id}",
     *     summary="Delete a location",
     *     tags={"Location"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the location to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Location deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Location not found"
     *     )
     * )
     */

    public function destroy($id)
    {
        $location = Warehouse::find($id);
        if(!$location) {
            return response()->json(['message' => 'Location not found'], 404);
        }
        $location->delete();
        return response()->json(['message' => 'Location deleted successfully', 'id' => $id]);
    }
    /**
     * @OA\Get(
     *     path="/api/v1/location_show/{id}",
     *     summary="Retrieve a specific location",
     *     tags={"Location"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the location to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Location retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Location not found"
     *     )
     * )
     */
    public function show($id)
    {
        $location = Warehouse::find($id);
        if (!$location) {
            return response()->json(['message' => 'Location not found'], 404);
        }
        return response()->json(['message' => 'Location retrieved successfully', 'location' => $location]);
    }
}

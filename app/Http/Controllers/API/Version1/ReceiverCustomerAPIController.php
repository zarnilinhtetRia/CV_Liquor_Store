<?php

namespace App\Http\Controllers\API\Version1;

use Illuminate\Http\Request;
use App\Models\SenderCustomer;
use App\Models\ReceiverCustomer;
use App\Http\Controllers\Controller;

class ReceiverCustomerAPIController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/receiver_customers",
     *     summary="Retrieve a list of all receiver customers",
     *     tags={"Receiver Customer"},
     *     @OA\Response(
     *         response=200,
     *         description="Receiver customer list retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Receiver customer list not found"
     *     )
     * )
     */

    public function index()
    {
        $receiverCustomers = ReceiverCustomer::all();
        return response()->json([
            'message' => 'Receiver customers retrieved successfully',
            'receiver_customers' => $receiverCustomers,
        ], 200);
    }


    /**
     * @OA\Post(
     *     path="/api/v1/receiver_customer_register",
     *     summary="Store a new receiver customer",
     *     tags={"Receiver Customer"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example=""),
     *             @OA\Property(property="phone", type="string", example=""),
     *             @OA\Property(property="address", type="string", example=""),
     *            @OA\Property(property="location", type="string", example="1"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Receiver customer created successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $receiverCustomer = new ReceiverCustomer();
        $receiverCustomer->name = $request->name;
        $receiverCustomer->phone = $request->phone ?? '';
        $receiverCustomer->address = $request->address ?? '';
        $receiverCustomer->location = $request->location ?? '';
        $receiverCustomer->save();

        return response()->json([
            'message' => 'Receiver customer registered successfully',
            'receiver_customer' => $receiverCustomer,
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/receiver_customer_update/{id}",
     *     summary="Update a receiver customer",
     *     tags={"Receiver Customer"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the receiver customer to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example=""),
     *             @OA\Property(property="phone", type="string", example=""),
     *             @OA\Property(property="address", type="string", example=""),
     *            @OA\Property(property="location", type="string", example="1"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Receiver customer updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Receiver customer not found"
     *     )
     * )
     */

    public function update(Request $request, $id)
    {
        $receiverCustomer = ReceiverCustomer::find($id);
        if (!$receiverCustomer) {
            return response()->json(['message' => 'Receiver customer not found'], 404);
        }
        $receiverCustomer->name = $request->name;
        $receiverCustomer->phone = $request->phone ?? '';
        $receiverCustomer->address = $request->address ?? '';
        $receiverCustomer->location = $request->location ?? '';
        $receiverCustomer->save();

        return response()->json(['message' => 'Receiver customer updated successfully', 'receiver_customer' => $receiverCustomer]);
    }
    /**
     * @OA\Delete(
     *     path="/api/v1/receiver_customer_destroy/{id}",
     *     summary="Delete a receiver customer",
     *     tags={"Receiver Customer"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the receiver customer to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Receiver customer deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Receiver customer not found"
     *     )
     * )
     */

    public function destroy($id)
    {
        $receiverCustomer = ReceiverCustomer::find($id);
        if (!$receiverCustomer) {
            return response()->json(['message' => 'Receiver customer not found'], 404);
        }
        $receiverCustomer->delete();
        return response()->json(['message' => 'Receiver customer deleted successfully', 'id' => $id]);
    }
    /**
     * @OA\Get(
     *     path="/api/v1/receiver_customer_show/{id}",
     *     summary="Retrieve a specific receiver customer",
     *     tags={"Receiver Customer"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the receiver customer to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Receiver customer retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Receiver customer not found"
     *     )
     * )
     */
    public function show($id)
    {
        $receiverCustomer = ReceiverCustomer::find($id);
        if (!$receiverCustomer) {
            return response()->json(['message' => 'Receiver customer not found'], 404);
        }
        return response()->json(['message' => 'Receiver customer retrieved successfully', 'receiver_customer' => $receiverCustomer]);
    }
}

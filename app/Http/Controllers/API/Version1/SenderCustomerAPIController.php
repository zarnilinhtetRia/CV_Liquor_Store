<?php

namespace App\Http\Controllers\API\Version1;

use Illuminate\Http\Request;
use App\Models\SenderCustomer;
use App\Http\Controllers\Controller;

class SenderCustomerAPIController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/sender_customers",
     *     summary="Retrieve a list of all sender customers",
     *     tags={"Sender Customer"},
     *     @OA\Response(
     *         response=200,
     *         description="Sender customer list retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Sender customer list not found"
     *     )
     * )
     */

    public function index()
    {
        $senderCustomers = SenderCustomer::all();
        return response()->json([
            'message' => 'Sender customers retrieved successfully',
            'sender_customers' => $senderCustomers,
        ], 200);
    }


    /**
     * @OA\Post(
     *     path="/api/v1/sender_customer_register",
     *     summary="Store a new sender customer",
     *     tags={"Sender Customer"},
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
     *         description="Sender customer created successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $senderCustomer = new SenderCustomer();
        $senderCustomer->name = $request->name;
        $senderCustomer->phone = $request->phone ?? '';
        $senderCustomer->address = $request->address ?? '';
        $senderCustomer->location = $request->location ?? '';
        $senderCustomer->save();

        return response()->json([
            'message' => 'Sender customer registered successfully',
            'sender_customer' => $senderCustomer,
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/sender_customer_update/{id}",
     *     summary="Update a sender customer",
     *     tags={"Sender Customer"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the sender customer to update",
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
     *         description="Sender customer updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Sender customer not found"
     *     )
     * )
     */

    public function update(Request $request, $id)
    {
        $senderCustomer = SenderCustomer::find($id);
        if (!$senderCustomer) {
            return response()->json(['message' => 'Sender customer not found'], 404);
        }
        $senderCustomer->name = $request->name;
        $senderCustomer->phone = $request->phone ?? '';
        $senderCustomer->address = $request->address ?? '';
        $senderCustomer->location = $request->location ?? '';
        $senderCustomer->save();

        return response()->json(['message' => 'Sender customer updated successfully', 'sender_customer' => $senderCustomer]);
    }
    /**
     * @OA\Delete(
     *     path="/api/v1/sender_customer_destroy/{id}",
     *     summary="Delete a sender customer",
     *     tags={"Sender Customer"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the sender customer to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sender customer deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Sender customer not found"
     *     )
     * )
     */

    public function destroy($id)
    {
        $senderCustomer = SenderCustomer::find($id);
        if (!$senderCustomer) {
            return response()->json(['message' => 'Sender customer not found'], 404);
        }
        $senderCustomer->delete();
        return response()->json(['message' => 'Sender customer deleted successfully', 'id' => $id]);
    }
    /**
     * @OA\Get(
     *     path="/api/v1/sender_customer_show/{id}",
     *     summary="Retrieve a specific sender customer",
     *     tags={"Sender Customer"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the sender customer to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sender customer retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Sender customer not found"
     *     )
     * )
     */
    public function show($id)
    {
        $senderCustomer = SenderCustomer::find($id);
        if (!$senderCustomer) {
            return response()->json(['message' => 'Sender customer not found'], 404);
        }
        return response()->json(['message' => 'Sender customer retrieved successfully', 'sender_customer' => $senderCustomer]);
    }
}

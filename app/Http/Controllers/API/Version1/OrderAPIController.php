<?php

namespace App\Http\Controllers\API\Version1;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;
class OrderAPIController extends Controller
{
    //
    /**
     * @OA\Get(
     *     path="/api/v1/orders",
     *     summary="Retrieve a list of all orders",
     *     tags={"Order"},
     *     @OA\Response(
     *         response=200,
     *         description="Order list retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order list not found"
     *     )
     * )
     */

    public function index(){
        $orders= Order::latest()->get();
        return response()->json([
            'message' => 'Orders retrieved successfully',
            'orders' => $orders,
        ], 200);
    }
    /**
     * @OA\Post(
     *     path="/api/v1/order_register",
     *     summary="Store a new order",
     *     tags={"Order"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"shop_id","manager_id","order_id","order_status","order_date","sender_id","receiver_id","order_weight","sub_amount","parcel_price","code_price","total_amount","cash_status","note","order_type"},
     *             @OA\Property(property="shop_id", type="string", example=""),
     *             @OA\Property(property="manager_id", type="string", example=""),
     *             @OA\Property(property="order_id", type="string", example=""),
     *             @OA\Property(property="order_status", type="string", example=""),
     *             @OA\Property(property="order_date", type="string", format="date",example="2025-11-07"),
     *             @OA\Property(property="sender_id", type="string", example=""),
     *             @OA\Property(property="receiver_id", type="string", example=""),
     *             @OA\Property(property="order_weight", type="string", example=""),
     *             @OA\Property(property="sub_amount", type="string", example=""),
     *             @OA\Property(property="parcel_price", type="string", example=""),
     *            @OA\Property(property="code_price", type="integer", example=""),
     *             @OA\Property(property="total_amount", type="string", example=""),
     *             @OA\Property(property="cash_status", type="string", example=""),
     *             @OA\Property(property="note", type="string", example=""),
     *             @OA\Property(property="order_type", type="string", example=""),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order created successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */
    public function store(Request $request)
    {
       try{
            $manager = new ImageManager(new Driver());
            $photos = [];

            foreach (['order_photo1', 'order_photo2', 'order_photo3', 'order_photo4', 'order_photo5', 'order_photo6'] as $photoField) {
                if ($request->hasFile($photoField)) {
                    $file = $request->file($photoField);
                    $filename = time() . '_' . $photoField . '.' . $file->getClientOriginalExtension();
                    $directory = public_path('images/orders/');

                    if (!file_exists($directory)) {
                        mkdir($directory, 0777, true);
                    }

                    $path = $directory . $filename;
                    $manager->read($file)
                        ->scale(width: 400) // auto maintains aspect ratio
                        ->resize(400, 400, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        })
                        ->save($path, quality: 75);

                    $photos[$photoField] =  $filename;
                } else {
                    $photos[$photoField] = null;
                }
            }
            $order = new Order();
            $order->shop_id = $request->shop_id;
            $order->manager_id = $request->manager_id;
            $order->order_id = $request->order_id;
            $order->order_status = $request->order_status;
            $order->order_date = $request->order_date;
            $order->sender_id = $request->sender_id;
            $order->receiver_id = $request->receiver_id;
            $order->order_weight = $request->order_weight;
            $order->sub_amount = $request->sub_amount;
            $order->parcel_price = $request->parcel_price;
            $order->code_price = $request->code_price;
            $order->total_amount = $request->total_amount;
            $order->cash_status = $request->cash_status;
            $order->note = $request->note;
            $order->order_type = $request->order_type;
            $order->order_photo1 = $photos['order_photo1'];
            $order->order_photo2 = $photos['order_photo2'];
            $order->order_photo3 = $photos['order_photo3'];
            $order->order_photo4 = $photos['order_photo4'];
            $order->order_photo5 = $photos['order_photo5'];
            $order->order_photo6 = $photos['order_photo6'];
            $order->save();

            return response()->json([
                'message' => 'Order registered successfully',
                'order' => $order,
            ], 201);
       }catch(\Exception $e){
            return response()->json([
                'message' => 'Order registration failed',
                'error' => $e->getMessage(),
            ], 400);
       }
    }
    /**
     * @OA\Put(
     *     path="/api/v1/order_update/{id}",
     *     summary="Update a order",
     *     tags={"Order"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the order to update",
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     * *             required={"shop_id","manager_id","order_id","order_status","order_date","sender_id","receiver_id","order_weight","sub_amount","parcel_price","code_price","total_amount","cash_status","note","order_type"},
     *             @OA\Property(property="shop_id", type="string", example=""),
     *             @OA\Property(property="manager_id", type="string", example=""),
     *             @OA\Property(property="order_id", type="string", example=""),
     *             @OA\Property(property="order_status", type="string", example=""),
     *             @OA\Property(property="order_date", type="string", format="date",example="2025-11-07"),
     *             @OA\Property(property="sender_id", type="string", example=""),
     *             @OA\Property(property="receiver_id", type="string", example=""),
     *             @OA\Property(property="order_weight", type="string", example=""),
     *             @OA\Property(property="sub_amount", type="string", example=""),
     *             @OA\Property(property="parcel_price", type="string", example=""),
     *            @OA\Property(property="code_price", type="integer", example=""),
     *             @OA\Property(property="total_amount", type="string", example=""),
     *             @OA\Property(property="cash_status", type="string", example=""),
     *             @OA\Property(property="note", type="string", example=""),
     *             @OA\Property(property="order_type", type="string", example=""),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     )
     * )
     */

    public function update(Request $request,$id){
        // return $request->all();
        try {
            $order = Order::find($id);
            if (!$order) {
                return response()->json([
                    'message' => 'Order not found',
                ], 404);
            }
            $manager = new ImageManager(new Driver());
            $photos = [];

            // foreach (['order_photo1', 'order_photo2', 'order_photo3', 'order_photo4', 'order_photo5', 'order_photo6'] as $photoField) {
            //     if ($request->hasFile($photoField)) {
            //         $file = $request->file($photoField);
            //         $filename = time() . '_' . $photoField . '.' . $file->getClientOriginalExtension();
            //         $directory = public_path('images/orders/');

            //         if (!file_exists($directory)) {
            //             mkdir($directory, 0777, true);
            //         }

            //         $path = $directory . $filename;
            //         $manager->read($file)
            //             ->scale(width: 400) // auto maintains aspect ratio
            //             ->resize(400, 400, function ($constraint) {
            //                 $constraint->aspectRatio();
            //                 $constraint->upsize();
            //             })
            //             ->save($path, quality: 75);

            //         $photos[$photoField] =  $filename;
            //     } else {
            //         $photos[$photoField] = null;
            //     }
            // }

            $order->shop_id = $request->shop_id;
            $order->manager_id = $request->manager_id;
            $order->order_id = $request->order_id;
            // $order->order_status = $request->order_status;
            $order->order_date = $request->order_date;
            $order->sender_id = $request->sender_id;
            $order->receiver_id = $request->receiver_id;
            $order->order_weight = $request->order_weight;
            $order->sub_amount = $request->sub_amount;
            $order->parcel_price = $request->parcel_price;
            $order->code_price = $request->code_price;
            $order->total_amount = $request->total_amount;
            $order->cash_status = $request->cash_status;
            $order->note = $request->note;
            $order->order_type = $request->order_type;
            // $order->order_photo1 = $photos['order_photo1'];
            // $order->order_photo2 = $photos['order_photo2'];
            // $order->order_photo3 = $photos['order_photo3'];
            // $order->order_photo4 = $photos['order_photo4'];
            // $order->order_photo5 = $photos['order_photo5'];
            // $order->order_photo6 = $photos['order_photo6'];
            $order->save();
            return response()->json([
                'message' => 'Order updated successfully',
                'order' => $order,
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Order update failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
    /**
     * @OA\Delete(
     *     path="/api/v1/order_destroy/{id}",
     *     summary="Delete a order",
     *     tags={"Order"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Order to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     )
     * )
     */
    public function destroy($id){
        try {
            $order = Order::find($id);
            if (!$order) {
                return response()->json([
                    'message' => 'Order not found',
                ], 404);
            }
            $order->delete();
            return response()->json([
                'message' => 'Order deleted successfully',
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Order delete failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/order_show/{id}",
     *     summary="Retrieve a specific order",
     *     tags={"Order"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Order to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     )
     * )
     */

    public function show($id){
        try {
            $order = Order::find($id);
            if (!$order) {
                return response()->json([
                    'message' => 'Order not found',
                ], 404);
            }
            return response()->json([
                'message' => 'Order retrieved successfully',
                'order' => $order,
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Order retrieve failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
    public function change_status($id, Request $request){
        try {
            $order = Order::find($id);
            if (!$order) {
                return response()->json([
                    'message' => 'Order not found',
                ], 404);
            }
            $order->order_status = $request->order_status;
            $order->save();
            return response()->json([
                'message' => 'Order status changed successfully',
                'order' => $order,
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Order status change failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

}

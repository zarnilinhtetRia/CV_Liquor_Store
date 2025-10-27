<?php

namespace App\Http\Controllers\API\Version1;

use App\Models\User;
use Illuminate\Http\Request;


// use Intervention\Image\Facades\Image;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Http\Exceptions\HttpResponseException;



class LoginAPIController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/v1/users",
     *     summary="Retrieve a list of all users",
     *     tags={"User"},
     *     @OA\Response(
     *         response=200,
     *         description="User list retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User list not found"
     *     )
     * )
     */
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/user_register",
     *     summary="Store a new user",
     *     tags={"User"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"account_id","name", "email", "password", "type","confirm_password","father_name","phone","address"},
     *             @OA\Property(property="account_id", type="string", example=""),
     *             @OA\Property(property="name", type="string", example=""),
     *             @OA\Property(property="email", type="email", example="example@gmail.com"),
     *             @OA\Property(property="password", type="password", example=""),
     *             @OA\Property(property="password_confirmation", type="password", example=""),
     *             @OA\Property(property="type", type="string", example="Staff"),
     *             @OA\Property(property="father_name", type="string", example=""),
     *             @OA\Property(property="phno", type="string", example=""),
     *             @OA\Property(property="address", type="string", example=""),
     *             @OA\Property(property="social_media", type="string", example=""),
     *            @OA\Property(property="location", type="integer", example="1"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */
    public function user_register(Request $request)
    {
        try {
        // Validate the request data
        $validated = $request->validate([
            'account_id' => 'required',
            'type' => 'required|string',
            'profile_photo1' => 'nullable|image|mimes:jpg,jpeg,png',
            'profile_photo2' => 'nullable|image|mimes:jpg,jpeg,png',
            'profile_photo3' => 'nullable|image|mimes:jpg,jpeg,png',
            'profile_photo4' => 'nullable|image|mimes:jpg,jpeg,png',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'father_name' => 'nullable|string|max:255',
            'phno' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'social_media' => 'nullable|string|max:100',
                'location' => 'nullable|integer',

            ]);
        // dd( $validated);
        $manager = new ImageManager(new Driver());
        $photos = [];

        foreach (['profile_photo1', 'profile_photo2', 'profile_photo3', 'profile_photo4'] as $photoField) {
            if ($request->hasFile($photoField)) {
                $file = $request->file($photoField);
                $filename = time() . '_' . $photoField . '.' . $file->getClientOriginalExtension();
                $directory = public_path('images/profiles/');

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
        // Create a new user
        $user = User::create([
            'account_id' => $validated['account_id'],
            'type' => $validated['type'],
            'profile_photo1' => $photos['profile_photo1'],
            'profile_photo2' => $photos['profile_photo2'],
            'profile_photo3' => $photos['profile_photo3'],
            'profile_photo4' => $photos['profile_photo4'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'father_name' => $validated['father_name'] ?? null,
            'phno' => $validated['phno'] ?? null,
            'address' => $validated['address'] ?? null,
            'social_media' => $validated['social_media'] ?? null,
                'location' => $validated['location'] ?? null,
            ]);
            // $size = filesize($path);

            // $sizeKB = $size / 1024;
            // $sizeMB = $size / (1024 * 1024);

            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user,
                // 'image_size' => ['size_bytes' => $size, 'size_kb' => $sizeKB, 'size_mb' => $sizeMB],
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/user_update/{id}",
     *     summary="Update a user",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user to update",
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"account_id","name", "email", "password", "type","confirm_password","father_name","phone","address"},
     *             @OA\Property(property="account_id", type="string", example=""),
     *             @OA\Property(property="name", type="string", example=""),
     *             @OA\Property(property="email", type="email", example="example@gmail.com"),
     *             @OA\Property(property="password", type="password", example=""),
     *             @OA\Property(property="password_confirmation", type="password", example=""),
     *             @OA\Property(property="type", type="string", example="Staff"),
     *             @OA\Property(property="father_name", type="string", example=""),
     *             @OA\Property(property="phno", type="string", example=""),
     *             @OA\Property(property="address", type="string", example=""),
     *             @OA\Property(property="social_media", type="string", example=""),
     *            @OA\Property(property="location", type="integer", example="1"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */



    public function update(Request $request, $id)

    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        try {
            $validated = $request->validate([
                'account_id' => 'required',
                'type' => 'required|string',
                'profile_photo1' => 'nullable|image|mimes:jpg,jpeg,png',
                'profile_photo2' => 'nullable|image|mimes:jpg,jpeg,png',
                'profile_photo3' => 'nullable|image|mimes:jpg,jpeg,png',
                'profile_photo4' => 'nullable|image|mimes:jpg,jpeg,png',
                'name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users', 'email')->ignore($id),
                ],
                'password' => 'required|string|min:6|confirmed',
                'father_name' => 'nullable|string|max:255',
                'phno' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'social_media' => 'nullable|string|max:100',
                'location' => 'nullable|integer',

            ]);
            $manager = new ImageManager(new Driver());
            $photos = [];

            foreach (['profile_photo1', 'profile_photo2', 'profile_photo3', 'profile_photo4'] as $photoField) {
                if ($request->hasFile($photoField)) {
                    $file = $request->file($photoField);
                    $filename = time() . '_' . $photoField . '.' . $file->getClientOriginalExtension();
                    $directory = public_path('images/profiles/');

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

            $user->update([
                'account_id' => $validated['account_id'],
                'type' => $validated['type'],
                'profile_photo1' => $photos['profile_photo1'],
                'profile_photo2' => $photos['profile_photo2'],
                'profile_photo3' => $photos['profile_photo3'],
                'profile_photo4' => $photos['profile_photo4'],
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'father_name' => $validated['father_name'] ?? null,
                'phno' => $validated['phno'] ?? null,
                'address' => $validated['address'] ?? null,
                'social_media' => $validated['social_media'] ?? null,
                'location' => $validated['location'] ?? null,
            ]);
            // $size = filesize($path);

            // $sizeKB = $size / 1024;
            // $sizeMB = $size / (1024 * 1024);

            return response()->json([
                'message' => 'User updated successfully',
                'user' => $user,
                // 'image_size' => ['size_bytes' => $size, 'size_kb' => $sizeKB, 'size_mb' => $sizeMB],
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/user_destroy/{id}",
     *     summary="Delete a user",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */

    public function  destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully', 'id' => $id]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/user_login",
     *     summary="User",
     *     tags={"User"},

     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="email", example="example@gmail.com"),
     *             @OA\Property(property="password", type="password", example=""),
     *
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Login successfully"
     *     ),@OA\Response(
     *         response=401,
     *         description="Login Failed"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */
    public function user_login(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'message' => 'Invalid input',
                'errors' => $validator->errors(),
            ], 400));
        }

        // Attempt to find the user by email
        $user = User::where('email', $request->email)->first();

        // Check if the user exists and the password is correct
        if ($user && Hash::check($request->password, $user->password)) {
            // Authentication successful
            $token = $user->createToken($request->email)->plainTextToken;
            return response()->json([
                'message' => 'Login successfully',
                'user' => $user,
                'token' => $token,
            ], 201);
        } else {
            // Authentication failed
            return response()->json([
                'message' => 'Login Failed. Invalid email or password.',
            ], 401);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/user_show/{id}",
     *     summary="Retrieve a specific user",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json(['message' => 'User retrieved successfully', 'user' => $user]);
    }
}

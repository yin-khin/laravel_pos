<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Login user and create token
     */
    public function index()
    {
        return response()->json([
            "list" => User::with('profile')->get()
        ]);
    }

    // Login a user and return a JWT token
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get credentials
        $credentials = $request->only('email', 'password');

        try {
            // Attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }
        } catch (JWTException $e) {
            // Something went wrong with JWT
            return response()->json([
                'success' => false,
                'message' => 'Could not create token'
            ], 500);
        }

        // Get the authenticated user
        $user = Auth::user();
        
        // Load the profile relationship
        $user->load('profile');

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    /**
     * Register new user
     */
    public function register(Request $request)
    {
        // Only validate image if a new image is being uploaded
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'type' => 'sometimes|in:admin,user,manager,sales,inventory',
            'phone' => 'sometimes|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'address' => 'sometimes|string|max:255',
        ];
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $userType = $request->type ?? 'user';
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $userType,
        ]);

        // Create profile if additional data provided
        if ($request->has(['phone', 'address']) || $request->hasFile('image')) {
            $profileData = [
                'user_id' => $user->id,
                'type' => $userType
            ];
            
            if ($request->phone) $profileData['phone'] = $request->phone;
            if ($request->address) $profileData['address'] = $request->address;
            
            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('profiles', 'public');
                $profileData['image'] = $imagePath;
            }
            
            Profile::create($profileData);
        }

        // Generate JWT token
        try {
            $token = JWTAuth::fromUser($user);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not create token'
            ], 500);
        }

        // Load the profile relationship
        $user->load('profile');

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);
    }

    /**
     * Get user profile
     */
    public function profile(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $user->load('profile');

        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    /**
     * Update user profile
     */
    public function update(Request $request, string $id)
    {
        // Only validate image if a new image is being uploaded
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'sometimes|nullable|min:6',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'type' => 'nullable|in:admin,user,manager,sales,inventory'
        ];
        
        // Only add image validation if a file is being uploaded
        if ($request->hasFile('image')) {
            $rules['image'] = 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240';
        } else {
            $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240';
        }
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::findOrFail($id);
        
        // Update user basic info
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
        ];
        
        $userType = null;
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }
        
        if ($request->filled('type')) {
            $userType = $request->type;
            $userData['user_type'] = $userType;
        }
        
        $user->update($userData);
        
        // Update or create profile
        $profileData = [
            'user_id' => $user->id
        ];
        
        if ($userType) {
            $profileData['type'] = $userType;
        }
        
        if ($request->filled('phone')) {
            $profileData['phone'] = $request->phone;
        }
        
        if ($request->filled('address')) {
            $profileData['address'] = $request->address;
        }
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->profile && $user->profile->image) {
                Storage::disk('public')->delete($user->profile->image);
            }
            
            $imagePath = $request->file('image')->store('profiles', 'public');
            $profileData['image'] = $imagePath;
        }
        
        if ($user->profile) {
            $user->profile->update($profileData);
        } else {
            Profile::create($profileData);
        }
        
        // Load the profile relationship
        $user->load('profile');
        
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }

    /**
     * Logout user (invalidate token)
     */
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            
            return response()->json([
                'success' => true,
                'message' => 'Logout successful'
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to logout'
            ], 500);
        }
    }
}
<?php

namespace App\Http\Controllers\CRM\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', '!=', 'admin')
    ->latest()
    ->paginate(20);
        return view('crm.settings.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'nullable|string|max:20',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,counsellor,receptionist,teacher,staff',
            'status' => 'required|in:active,inactive',
            'monthly_target' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $this->uploadProfileImage($request->file('profile_image'));
        }

        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return back()->with('success', 'User added successfully.');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'mobile' => 'nullable|string|max:20',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin,counsellor,receptionist,teacher,staff',
            'status' => 'required|in:active,inactive',
            'monthly_target' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $this->uploadProfileImage($request->file('profile_image'));
        } else {
            unset($data['profile_image']);
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        return back()->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot delete your own logged-in account.');
        }

        $user->delete();
        return back()->with('success', 'User deleted successfully.');
    }

    private function uploadProfileImage($file): string
    {
        $directory = public_path('uploads/profile-images');

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $filename = 'profile-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($directory, $filename);

        return 'uploads/profile-images/' . $filename;
    }
}

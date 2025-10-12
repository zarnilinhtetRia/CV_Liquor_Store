<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        $user = User::find($id);
        return view('profile.edit', compact('user'));
    }
    /**
     * Update the user's profile information.
     *
     * @param  \App\Http\Requests\ProfileUpdateRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProfileUpdateRequest $request, $id)
    {
        $user = User::find($id);
        // $request->user()->fill($request->validated());
        $validatedData = $request->validated();
        $user->fill($validatedData);
        // $request->user()->save();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->type = $request->type;
        $user->level = $request->level;
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }
        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->input('new_password'));
        }
        $user->is_admin = $request->input('is_admin', false);
        $user->update();
        // return Redirect::route('profile.edit')->with('status', 'profile-updated');
        return redirect()->route('user')->with('status', 'profile-updated');
    }
    /**
     * Delete the user's account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        return redirect()->back()->with('delete', 'Delete');
        // $user = $request->user();
        // Auth::logout();
        // $user->delete();
        // $request->session()->invalidate();
        // $request->session()->regenerateToken();
        // return Redirect::to('/');
    }
}

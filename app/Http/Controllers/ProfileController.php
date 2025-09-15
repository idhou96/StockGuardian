<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function show(Request $request)
    {
        return view('profile.show', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return redirect()->route('profile.show')->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.show')->with('success', 'Mot de passe mis à jour avec succès.');
    }

    /**
     * Update user preferences.
     */
    public function updatePreferences(Request $request)
    {
        $request->validate([
            'language' => ['nullable', 'string', 'in:fr,en'],
            'theme' => ['nullable', 'string', 'in:light,dark'],
            'notifications' => ['nullable', 'boolean'],
        ]);

        // Stocker les préférences (vous pouvez créer une table preferences ou utiliser un JSON dans users)
        $preferences = [
            'language' => $request->input('language', 'fr'),
            'theme' => $request->input('theme', 'light'),
            'notifications' => $request->boolean('notifications'),
        ];

        $request->user()->update([
            'preferences' => json_encode($preferences)
        ]);

        return redirect()->route('profile.show')->with('success', 'Préférences mises à jour avec succès.');
    }

    /**
     * Update user avatar.
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            $avatarName = time() . '.' . $request->avatar->getClientOriginalExtension();
            $request->avatar->move(public_path('storage/avatars'), $avatarName);

            // Supprimer l'ancien avatar s'il existe
            if ($request->user()->avatar && file_exists(public_path('storage/avatars/' . $request->user()->avatar))) {
                unlink(public_path('storage/avatars/' . $request->user()->avatar));
            }

            $request->user()->update([
                'avatar' => $avatarName
            ]);
        }

        return redirect()->route('profile.show')->with('success', 'Photo de profil mise à jour avec succès.');
    }
}
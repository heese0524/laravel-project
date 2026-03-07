<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */

    /**
     * Update the user's profile information.
     */

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
    public function show()
            {
                $user = Auth::user();
                $posts = Post::where('user_id', $user->id)
                            ->orderBy('created_at', 'desc')
                            ->paginate(5); // ← 改成分页！

                return view('profile', compact('user', 'posts'));
            }

    
        public function edit()
        {
            return view('profile.edit', ['user' => Auth::user()]);
        }

        public function update(Request $request)
        {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . Auth::id(),
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 最大 2MB
            ]);

            $user = Auth::user();
            $user->name = $request->name;
            $user->email = $request->email;

            // 处理头像上传
            if ($request->hasFile('avatar')) {
                // 删除旧头像（如果不是默认首字母）
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }

                $path = $request->file('avatar')->store('avatars', 'public');
                $user->avatar = $path;
            }

            $user->save();

            return redirect()->route('profile')->with('success', '资料更新成功！');
        }
    //     public function show()
    // {
    //     $user = Auth::user();

    //     if (! $user) {
    //         return redirect()->route('login');
    //     }

    //     // 获取当前用户的文章（假设 Post 模型有 user_id 字段）
    //     $posts = Post::where('user_id', $user->id)
    //                 ->orderBy('created_at', 'desc')
    //                 ->get();

    //     return view('profile', compact('user', 'posts'));
    // }
}

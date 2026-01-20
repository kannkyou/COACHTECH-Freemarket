<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureProfileIsCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // 未ログインはスルー
        if (! $user) {
            return $next($request);
        }

        // プロフィール画面/更新処理/ログアウトは除外（無限ループ防止）
        if ($request->routeIs('mypage.profile.edit', 'mypage.profile.update', 'logout')) {
            return $next($request);
        }

        // 必須項目が未入力ならプロフィール編集へ
        if (empty($user->postal_code) || empty($user->address)) {
            return redirect()->route('mypage.profile.edit');
        }

        return $next($request);
    }
}
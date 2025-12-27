<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Paystack (and other external providers) cannot send Laravel CSRF tokens.
        // Without this, webhook POSTs will fail with 419 (Page Expired).
        $middleware->validateCsrfTokens(except: [
            'webhooks/paystack/subscription',
        ]);

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'ensure.profile.complete' => \App\Http\Middleware\EnsureProfileComplete::class,
            'ensure.user.type.checked' => \App\Http\Middleware\EnsureUserTypeChecked::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle PostTooLargeException (413 errors)
        $exceptions->render(function (\Illuminate\Http\Exceptions\PostTooLargeException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'File is too large. Maximum upload size is 20MB. Please try a smaller file.',
                ], 413);
            }

            return redirect()->back()
                ->with('error', 'File is too large. Maximum upload size is 20MB. Please try a smaller file.');
        });
    })->create();

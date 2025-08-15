# Towoju One-to-One Calls (Laravel Package)

One-on-one audio/video call scaffolding for Laravel apps. Handles **permissions**, **signaling events**, **availability**, and ships a minimal **Blade UI + JS** stub using **WebRTC** and **Laravel Echo**.

> Streams are **peer-to-peer via WebRTC**. Laravel handles **auth, permissions, and event signaling**.

## Features
- Toggle **who can initiate calls** (Super Admin only).
- All users can receive calls by default.
- Events: Incoming, Accepted, Declined, Ended, Availability.
- Private broadcast channels: `calls.user.{id}`
- Minimal floating window UI with mic/video/switch camera buttons.
- Availability status via cache.
- API endpoints secured with `auth:sanctum` (adjust as needed).

## Install

```bash
composer require towoju/one2one-calls
php artisan vendor:publish --tag=one2one-calls-config
php artisan vendor:publish --tag=one2one-calls-migrations
php artisan migrate
php artisan vendor:publish --tag=one2one-calls-assets
```

Ensure broadcasting is configured (Pusher or `laravel-websockets`).

Add a meta tag for the authenticated user ID on pages that use the call UI:

```blade
<meta name="user-id" content="{{ auth()->id() }}">
```

Include Echo and the package JS (after configuring Echo):

```blade
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="{{ mix('js/app.js') }}"></script> {{-- Echo config in your app --}}
<script src="{{ asset('js/vendor/one2one-calls/call.js') }}"></script>
```

Use the Blade call view somewhere (optional):

```blade
@include('one2one-calls::call')
```

Super Admin permission UI:

```
GET  /one2one/permissions
PATCH /one2one/permissions/{user}
```

API (default `auth:sanctum`):

```
POST /api/one2one/availability { available: true|false }
GET  /api/one2one/availability/{userId}
POST /api/one2one/calls { receiver_id, metadata? }
POST /api/one2one/calls/{uuid}/accept
POST /api/one2one/calls/{uuid}/decline
POST /api/one2one/calls/{uuid}/end
```

## Notes

- Replace or extend the JS to exchange **SDP/ICE** over Echo events for a full WebRTC flow.
- To lock Super Admin by role, set `super_admin_role` in `config/one2one-calls.php`. Fallback boolean column `is_super_admin` is also supported.
- Middleware `EnsureCanInitiateCall` blocks unauthorized call attempts.
- Presence and push notifications can be added on top of this scaffold.

## License
MIT © Emmanuel A Towoju

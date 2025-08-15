@extends('layouts.app')

@section('content')
<div id="call-root" class="fixed bottom-4 right-4 w-80 h-48 bg-black text-white rounded shadow-lg overflow-hidden hidden">
    <div class="flex items-center justify-between p-2 bg-gray-900">
        <span class="text-sm">One-to-One Call</span>
        <div class="space-x-2">
            <button id="btn-minimize" class="text-xs px-2 py-1 bg-gray-700 rounded">_</button>
            <button id="btn-end" class="text-xs px-2 py-1 bg-red-600 rounded">End</button>
        </div>
    </div>
    <div class="relative w-full h-full">
        <video id="remoteVideo" playsinline autoplay class="absolute top-0 left-0 w-full h-full object-cover"></video>
        <video id="localVideo" playsinline autoplay muted class="absolute bottom-2 right-2 w-24 h-16 border-2 border-white object-cover rounded"></video>
    </div>
    <div class="flex items-center justify-center gap-3 p-2 bg-gray-900">
        <button id="btn-mic" class="text-xs px-2 py-1 bg-gray-700 rounded">Mute Mic</button>
        <button id="btn-video" class="text-xs px-2 py-1 bg-gray-700 rounded">Video Off</button>
        <button id="btn-switch" class="text-xs px-2 py-1 bg-gray-700 rounded">Switch Cam</button>
    </div>
</div>

<!-- Incoming banner -->
<div id="incoming-banner" class="hidden fixed top-4 right-4 bg-white border shadow-lg rounded p-3">
    <div class="font-semibold mb-2">Incoming Call</div>
    <div class="flex gap-2">
        <button id="btn-accept" class="px-3 py-1 bg-green-600 text-white rounded">Accept</button>
        <button id="btn-decline" class="px-3 py-1 bg-red-600 text-white rounded">Decline</button>
    </div>
</div>
@endsection

@push('scripts')
<!-- NOTE: In your app, include Echo + Pusher (or websockets) JS as usual -->
<script src="//cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="{{ asset('js/vendor/one2one-calls/call.js') }}"></script>
@endpush

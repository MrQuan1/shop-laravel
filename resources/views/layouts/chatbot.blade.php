@push('styles')
<link rel="stylesheet" href="{{ asset('frontend/css/chatbot.css') }}">
@endpush

<div id="chatbot-container">
    @include('frontend.layouts.chatbot')
</div>

@push('scripts')
<script src="{{ asset('frontend/js/chatbot.js') }}"></script>
@endpush 
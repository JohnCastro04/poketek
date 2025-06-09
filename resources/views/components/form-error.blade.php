@props(['messages' => []])

@if ($messages)
    <div class="error-container mt-2">
        @foreach ((array) $messages as $message)
            <div class="error-message">
                <i class="bi bi-exclamation-diamond error-icon"></i>
                <span class="error-text">{{ $message }}</span>
            </div>
        @endforeach
    </div>
@endif
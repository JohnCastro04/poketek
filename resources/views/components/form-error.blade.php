@props(['messages' => []])

@if ($messages)
    <div class="error-container mt-2">
        @foreach ((array) $messages as $message)
            <div class="error-message">
                <svg class="error-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="error-text">{{ $message }}</span>
            </div>
        @endforeach
    </div>
@endif

<style>
.error-container {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.error-message {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    border: 1px solid #fca5a5;
    border-radius: 8px;
    color: #dc2626;
    font-size: 0.875rem;
    font-weight: 500;
    animation: slideIn 0.3s ease-out;
    box-shadow: 0 2px 4px rgba(220, 38, 38, 0.1);
}

.error-icon {
    width: 18px;
    height: 18px;
    margin-right: 10px;
    flex-shrink: 0;
    color: #dc2626;
}

.error-text {
    line-height: 1.4;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@if($review)
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="bi bi-star-fill text-gold me-2"></i>Your Review</h5>
        </div>
        <div class="card-body p-4">
            <div class="text-warning mb-2">
                @for($i = 1; $i <= 5; $i++)
                    <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                @endfor
            </div>
            <p class="mb-1">{{ $review->review_text }}</p>
            <small class="text-muted">Submitted {{ $review->created_at->format('M d, Y') }}</small>
        </div>
    </div>
@endif

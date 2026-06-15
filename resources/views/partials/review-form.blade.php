<div class="card border-0 shadow-sm review-form-card">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="bi bi-star me-2 text-gold"></i>Rate Your Experience</h5>
    </div>
    <div class="card-body p-4">
        <p class="text-muted small mb-3">
            Reviews are only available after your service is completed.
        </p>
        <form method="POST" action="{{ $action }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Rating</label>
                <div class="review-stars d-flex gap-2 flex-wrap">
                    @for($i = 5; $i >= 1; $i--)
                        <label class="review-star-label">
                            <input type="radio" name="rating" value="{{ $i }}" class="d-none review-star-input"
                                   @checked(old('rating') == $i) required>
                            <span class="review-star-btn">
                                <i class="bi bi-star-fill"></i>
                                <small class="d-block">{{ $i }}</small>
                            </span>
                        </label>
                    @endfor
                </div>
                @error('rating')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="review_text_{{ $formId }}" class="form-label fw-semibold">Your review</label>
                <textarea class="form-control @error('review_text') is-invalid @enderror"
                          id="review_text_{{ $formId }}" name="review_text" rows="4" required
                          placeholder="Share your experience with this lawyer...">{{ old('review_text') }}</textarea>
                @error('review_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-btn-gold">
                <i class="bi bi-send me-1"></i> Submit Review
            </button>
        </form>
    </div>
</div>

@once
    @push('styles')
    <style>
        .review-star-btn {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            padding: 0.5rem 0.75rem;
            border: 2px solid #dee2e6;
            border-radius: 0.5rem;
            cursor: pointer;
            color: #adb5bd;
            transition: all 0.15s;
        }
        .review-star-input:checked + .review-star-btn,
        .review-star-label:hover .review-star-btn {
            border-color: var(--lawsphere-gold, #c9a227);
            color: var(--lawsphere-gold, #c9a227);
            background: #fffdf5;
        }
    </style>
    @endpush
@endonce

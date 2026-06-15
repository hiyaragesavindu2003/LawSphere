@extends('layouts.app')

@section('title', 'Find a Lawyer')

@section('content')
<section class="lawyer-list-hero py-5 mb-4">
    <div class="container">
        <h1 class="mb-2"><i class="bi bi-search me-2"></i>Find a Lawyer</h1>
        <p class="lead mb-0">Browse approved legal professionals by specialization, experience, and client ratings.</p>
    </div>
</section>

<div class="container pb-5">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('lawyers.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="{{ request('search') }}" placeholder="Name or specialization...">
                </div>
                <div class="col-md-3">
                    <label for="specialization" class="form-label">Specialization</label>
                    <select class="form-select" id="specialization" name="specialization">
                        <option value="">All specializations</option>
                        @foreach($specializations as $spec)
                            <option value="{{ $spec }}" @selected(request('specialization') === $spec)>
                                {{ $spec }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="min_rating" class="form-label">Min. rating</label>
                    <select class="form-select" id="min_rating" name="min_rating">
                        <option value="">Any</option>
                        @foreach([5, 4, 3] as $rating)
                            <option value="{{ $rating }}" @selected(request('min_rating') == $rating)>
                                {{ $rating }}+ stars
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="sort" class="form-label">Sort by</label>
                    <select class="form-select" id="sort" name="sort">
                        <option value="rating" @selected(request('sort', 'rating') === 'rating')>Top rated</option>
                        <option value="experience" @selected(request('sort') === 'experience')>Experience</option>
                        <option value="name" @selected(request('sort') === 'name')>Name</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-btn-navy w-100">
                        <i class="bi bi-funnel"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <p class="text-muted mb-4">{{ $lawyers->total() }} lawyer{{ $lawyers->total() !== 1 ? 's' : '' }} found</p>

    @if($lawyers->isEmpty())
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="card-body">
                <i class="bi bi-person-x display-4 text-muted mb-3"></i>
                <h5>No lawyers found</h5>
                <p class="text-muted mb-3">Try adjusting your search or filters.</p>
                <a href="{{ route('lawyers.index') }}" class="btn btn-btn-gold">Clear filters</a>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($lawyers as $lawyer)
                <div class="col-md-6 col-lg-4">
                    <div class="card lawyer-card h-100 border-0 shadow-sm">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <img src="{{ $lawyer->user->profile_photo_url }}" alt="{{ $lawyer->user->name }}"
                                     class="lawyer-avatar rounded-circle">
                                <div>
                                    <h5 class="mb-1">{{ $lawyer->user->name }}</h5>
                                    <span class="badge lawyer-spec-badge">{{ $lawyer->specialization }}</span>
                                </div>
                            </div>

                            <div class="text-warning mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= round($lawyer->average_rating) ? '-fill' : '' }}"></i>
                                @endfor
                                <small class="text-muted ms-1">
                                    {{ number_format($lawyer->average_rating, 1) }}
                                    ({{ $lawyer->reviews_count }} review{{ $lawyer->reviews_count !== 1 ? 's' : '' }})
                                </small>
                            </div>

                            <p class="text-muted small mb-2">
                                <i class="bi bi-briefcase me-1"></i>{{ $lawyer->experience_years }} years experience
                            </p>

                            <p class="text-muted small flex-grow-1">
                                {{ Str::limit($lawyer->biography, 100) }}
                            </p>

                            <a href="{{ route('lawyers.show', $lawyer) }}" class="btn btn-btn-navy w-100 mt-3">
                                View Profile <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $lawyers->links() }}
        </div>
    @endif
</div>
@endsection

<?php

namespace App\Http\Controllers;

use App\Models\Lawyer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LawyerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Lawyer::query()
            ->approved()
            ->with('user')
            ->withCount('reviews');

        if ($request->filled('search')) {
            $search = $request->string('search')->trim();
            $query->where(function ($q) use ($search) {
                $q->where('specialization', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('specialization')) {
            $query->where('specialization', $request->specialization);
        }

        if ($request->filled('min_rating')) {
            $query->minRating((float) $request->min_rating);
        }

        $sort = $request->get('sort', 'rating');
        $query = match ($sort) {
            'experience' => $query->orderByDesc('experience_years'),
            'name' => $query->join('users', 'lawyers.user_id', '=', 'users.id')
                ->orderBy('users.name')
                ->select('lawyers.*'),
            default => $query->orderByDesc('average_rating')->orderByDesc('total_reviews'),
        };

        $lawyers = $query->paginate(9)->withQueryString();

        $specializations = Lawyer::approved()
            ->distinct()
            ->orderBy('specialization')
            ->pluck('specialization');

        return view('lawyers.index', compact('lawyers', 'specializations'));
    }

    public function show(Lawyer $lawyer): View
    {
        abort_unless($lawyer->is_approved, 404);

        $lawyer->load([
            'user',
            'reviews' => fn ($q) => $q->with('client.user')->latest()->take(10),
        ]);

        return view('lawyers.show', compact('lawyer'));
    }
}

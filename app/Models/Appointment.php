<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'lawyer_id',
        'appointment_date',
        'appointment_time',
        'status',
        'notes',
        'cancellation_reason',
        'reschedule_reason',
        'rescheduled_at',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
            'status' => AppointmentStatus::class,
            'rescheduled_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function lawyer(): BelongsTo
    {
        return $this->belongsTo(Lawyer::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', AppointmentStatus::Pending);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', now()->toDateString())
            ->whereIn('status', [AppointmentStatus::Pending, AppointmentStatus::Approved]);
    }

    public function getFormattedTimeAttribute(): string
    {
        return \Carbon\Carbon::parse($this->appointment_time)->format('h:i A');
    }

    public function getFormattedDateTimeAttribute(): string
    {
        return $this->appointment_date->format('M d, Y').' at '.$this->formatted_time;
    }
}

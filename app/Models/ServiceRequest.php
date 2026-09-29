<?php

namespace App\Models;

use App\Enums\ServiceRequestStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ServiceRequest extends Model
{
    protected $fillable = ['user_id', 'notes', 'status', 'internal_notes'];

    protected $attributes = ['status' => 'new'];

    protected function casts(): array
    {
        return ['status' => ServiceRequestStatus::class];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
    }
}

<?php

namespace IncadevUns\CoreDomain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StrategicPlan extends Model
{
    use HasFactory;

    protected $table = 'strategic_plans';

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'status',
        'user_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user()
    {
        $userModel = config('auth.providers.users.model', 'App\Models\User');

        return $this->belongsTo($userModel, 'user_id');
    }

    public function objectives()
    {
        return $this->hasMany(StrategicObjective::class, 'plan_id');
    }

    public function iniciatives()
    {
        return $this->hasMany(Iniciative::class, 'plan_id');
    }
}

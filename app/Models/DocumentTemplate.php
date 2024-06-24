<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentTemplate extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'content',
        'example_url',
        'type',
        'level',
        'status',
        'template_type',
        'created_by',
        'updated_by',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeName($query, $name)
    {
        return $query->where('name', $name);
    }

    public function scopeContent($query, $content)
    {
        return $query->where('content', $content);
    }

    public function scopeCreatedBy($query, $created_by)
    {
        return $query->where('created_by', $created_by);
    }

    public function scopeUpdatedBy($query, $updated_by)
    {
        return $query->where('updated_by', $updated_by);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%')
            ->orWhere('content', 'like', '%' . $search . '%');
    }

    public function scopeSort($query, $sort)
    {
        return $query->orderBy($sort['field'], $sort['order']);
    }

    public function scopeFilter($query, $filters)
    {
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query;
    }

    public function scopePaginate($query, $perPage)
    {
        return $query->paginate($perPage);
    }

    public function scopeGet($query)
    {
        return $query->get();
    }

    public function scopeFirst($query)
    {
        return $query->first();
    }

    public function scopeFind($query, $id)
    {
        return $query->find($id);
    }
}

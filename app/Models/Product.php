<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'code', 'category_id'];

    public function stock()
    {
        return $this->hasOne(Stock::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function productImages()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%')->orWhere('code', 'like', '%' . $search . '%');
    }
    public function scopeReport($query, string $start, string $end)
    {
        $startDate = Carbon::parse($start)->startOfDay();
        $endDate   = Carbon::parse($end)->endOfDay();
        $query->whereBetween('created_at', [$startDate, $endDate]);
        return $query->orderBy('created_at', 'desc');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoletosVencidosModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_amount',
        'amount',
        'due_date',
        'payment_date',
        'interest_amount_calculated',
        'fine_amount_calculated',
    ];

    /**
     * GetTable method
     *
     * @return string
     */
    public function getTable(): string
    {
        return 'boletosVencidos';
    }

    /**
     * FirstOrCreate method
     *
     * @param array $attributes
     * @param array $values
     *
     * @return mixed
     */
    public static function firstOrCreate(array $attributes, array $values = []): mixed
    {
        $existingRecord = self::where($attributes)->first();

        if ($existingRecord) {
            return $existingRecord;
        } else {
            return self::create($values);
        }
    }
}

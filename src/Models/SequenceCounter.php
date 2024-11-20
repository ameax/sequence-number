<?php

namespace Ameax\SequenceNumber\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read mixed $label
 * @property-read mixed $translations
 *
 * @method static Builder|\Ameax\SequenceNumber\Models\Sequence newModelQuery()
 * @method static Builder|Sequence newQuery()
 * @method static Builder|Sequence query()
 * @method static Builder|Sequence whereJsonContainsLocale(string $column, string $locale, ?mixed $value)
 * @method static Builder|Sequence whereJsonContainsLocales(string $column, array $locales, ?mixed $value)
 * @method static Builder|Sequence whereLocale(string $column, string $locale)
 * @method static Builder|Sequence whereLocales(string $column, array $locales)
 *
 * @property int $id
 * @property int $current_value
 * @property int|null $year
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static Builder|Sequence whereCheckDigitPosition($value)
 * @method static Builder|Sequence whereCheckDigitSeparator($value)
 * @method static Builder|Sequence whereCreatedAt($value)
 * @method static Builder|Sequence whereCurrentValue($value)
 * @method static Builder|Sequence whereId($value)
 * @method static Builder|Sequence whereIncrementBy($value)
 * @method static Builder|Sequence whereName($value)
 * @method static Builder|Sequence wherePrefix($value)
 * @method static Builder|Sequence whereResetYearly($value)
 * @method static Builder|Sequence whereSuffix($value)
 * @method static Builder|Sequence whereToken($value)
 * @method static Builder|Sequence whereUpdatedAt($value)
 * @method static Builder|Sequence whereUseCheckDigit($value)
 * @method static Builder|Sequence whereYear($value)
 * @method static Builder|Sequence whereYearFormat($value)
 * @method static Builder|Sequence whereYearSeparator($value)
 *
 * @property int $sequence_id
 * @property-read \Ameax\SequenceNumber\Models\Sequence $sequence
 *
 * @method static Builder|SequenceCounter whereSequenceId($value)
 *
 * @mixin \Eloquent
 */
class SequenceCounter extends Model
{
    protected $fillable = [
        'sequence_id',
        'current_value',
        'year',
    ];

    protected $casts = [
        'current_value' => 'integer',
        'year' => 'integer',
    ];

    public function getConnectionName()
    {
        if (config('sequence-number.connection')) {
            return config('sequence-number.connection');
        }

        return parent::getConnectionName();
    }

    public function sequence(): BelongsTo
    {
        return $this->belongsTo(Sequence::class);
    }
}

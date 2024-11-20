<?php

namespace Ameax\SequenceNumber\Models;

use Domain\Commons\Models\SequenceCounter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
 * @property string $token
 * @property string|null $prefix
 * @property string|null $suffix
 * @property int $current_value
 * @property int $increment_by
 * @property string $year_format
 * @property string|null $year_separator
 * @property bool $use_check_digit
 * @property string|null $check_digit_separator
 * @property string $check_digit_position
 * @property bool $reset_yearly
 * @property int|null $year
 * @property ?array $payload
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Commons\Models\SequenceCounter> $counters
 * @property-read int|null $counters_count
 *
 * @mixin \Eloquent
 */
class Sequence extends Model
{
    protected $fillable = [
        'token',
        'number_min_length',
        'start_value',
        'prefix',
        'suffix',
        'year_format',
        'year_separator',
        'use_check_digit',
        'check_digit_separator',
        'check_digit_position',
        'reset_yearly',
    ];

    protected $casts = [
        'year_format' => 'string',
        'year_separator' => 'string',
        'use_check_digit' => 'boolean',
        'check_digit_separator' => 'string',
        'check_digit_position' => 'string',
        'number_min_length' => 'integer',
        'start_value' => 'integer',
        'payload' => 'array',
    ];

    public function getConnectionName()
    {
        if (config('sequence-number.connection')) {
            return config('sequence-number.connection');
        }

        return parent::getConnectionName();
    }

    public function counters(): HasMany
    {
        return $this->hasMany(SequenceCounter::class);
    }
}

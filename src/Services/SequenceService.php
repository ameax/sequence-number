<?php

namespace Ameax\SequenceNumber\Services;

use Ameax\SequenceNumber\Models\Sequence;
use Ameax\SequenceNumber\Models\SequenceCounter;
use Exception;
use Illuminate\Support\Facades\DB;

class SequenceService
{
    protected ?Sequence $sequence;

    public static function make(): self
    {
        return new self;
    }

    /**
     * @throws Exception
     */
    public function byToken(string $token): self
    {
        $this->sequence = Sequence::where('token', $token)->first();

        if (! $this->sequence) {
            throw new Exception('Sequence not found.');
        }

        return $this;
    }

    /**
     * Generate the next number in the sequence.
     *
     * @throws Exception
     * @throws \Throwable
     */
    public function next(): string
    {
        return DB::transaction(function () {
            if (! $this->sequence) {
                throw new Exception('Sequence not loaded.');
            }

            // Load or initialize the sequence counter
            $counter = SequenceCounter::firstOrCreate(
                ['sequence_id' => $this->sequence->id],
                ['current_value' => 0, 'year' => now()->year]
            );

            // Handle yearly reset if applicable
            if ($this->sequence->reset_yearly && $counter->year !== now()->year) {
                $counter->update(['current_value' => 0, 'year' => now()->year]);
            }

            // Increment the current value
            $counter->increment('current_value');
            $nextValue = $counter->current_value;

            // Generate the formatted sequence number
            return $this->generateNumber($nextValue);
        });
    }

    /**
     * Generate the formatted number based on the sequence settings.
     */
    protected function generateNumber(int $value): string
    {
        if (! $this->sequence) {
            throw new Exception('Sequence not loaded.');
        }

        $strValue = (string) $value;

        if ($this->sequence->number_min_length && strlen($strValue) < $this->sequence->number_min_length) {
            $value = str_pad($strValue, $this->sequence->number_min_length, '0', STR_PAD_LEFT);
        }

        $prefix = $this->sequence->prefix ?? '';
        $suffix = $this->sequence->suffix ?? '';
        $year = $this->generateYear();
        $yearSeparator = $this->sequence->year_separator ?? '';
        $checkDigitSeparator = $this->sequence->check_digit_separator ?? '';
        $checkDigit = $this->sequence->use_check_digit
            ? $this->calculateCheckDigit($prefix.$year.$value.$suffix)
            : '';

        $number = "{$prefix}{$year}{$yearSeparator}";

        if ($this->sequence->use_check_digit && $this->sequence->check_digit_position === 'prefix') {
            $number .= "{$checkDigit}{$checkDigitSeparator}{$value}";
        } else {
            $number .= "{$value}{$checkDigitSeparator}{$checkDigit}";
        }

        $number .= "{$suffix}";

        return $number;
    }

    /**
     * Generate the year portion of the sequence.
     */
    protected function generateYear(): string
    {
        if (! $this->sequence) {
            throw new Exception('Sequence not loaded.');
        }

        switch ($this->sequence->year_format) {
            case '2-digits':
                return now()->format('y');
            case '4-digits':
                return now()->format('Y');
            default:
                return '';
        }
    }

    /**
     * Calculate a check digit for the given string.
     */
    protected function calculateCheckDigit(string $input): int
    {
        $normalized = preg_replace('/[^A-Z0-9]/', '', strtoupper($input)) ?? ''; // Normalize input
        $digits = str_split(strrev($normalized));
        $sum = 0;

        foreach ($digits as $index => $digit) {
            $digit = is_numeric($digit) ? (int) $digit : ord($digit) - 55; // Convert letters to numeric
            if ($index % 2 === 0) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $sum += $digit;
        }

        return (10 - ($sum % 10)) % 10;
    }

    /**
     * Validate a given sequence number.
     *
     * @throws Exception
     */
    public function validateSequenceNumber(string $sequenceNumber): bool
    {
        if (! $this->sequence || ! $this->sequence->use_check_digit) {
            throw new Exception('Sequence not loaded or check digit not enabled.');
        }

        // Normalize the input: remove separators and normalize case
        $normalized = preg_replace('/[^A-Z0-9]/', '', strtoupper($sequenceNumber)) ?? '';

        // Prepare normalized prefix and suffix
        $prefix = $this->sequence->prefix ? preg_replace('/[^A-Z0-9]/', '', strtoupper($this->sequence->prefix)) : '';
        $suffix = $this->sequence->suffix ? preg_replace('/[^A-Z0-9]/', '', strtoupper($this->sequence->suffix)) : '';

        // Remove known prefix
        if ($prefix && strpos($normalized, $prefix) === 0) {
            $normalized = substr($normalized, strlen($prefix));
        }

        // Remove known suffix
        if ($suffix && substr($normalized, -strlen($suffix)) === $suffix) {
            $normalized = substr($normalized, 0, -strlen($suffix));
        }

        // Extract year if applicable
        $year = '';
        if ($this->sequence->year_format !== 'none') {
            $yearLength = $this->sequence->year_format === '4-digits' ? 4 : 2;
            $yearPattern = $this->sequence->year_separator ? preg_quote($this->sequence->year_separator, '/') : '';
            $yearRegex = '/^(\d{'.$yearLength.'})'.$yearPattern.'/';

            if (preg_match($yearRegex, $normalized, $matches)) {
                $year = $matches[1];
                $normalized = preg_replace($yearRegex, '', $normalized);
            }
        }

        if ($this->sequence->check_digit_position === 'prefix') {
            $actualCheckDigit = $normalized[0] ?? '';
            $numberWithoutCheckDigit = substr($normalized ?? '', 1);
        } else {
            $actualCheckDigit = substr($normalized ?? '', -1);
            $numberWithoutCheckDigit = substr($normalized ?? '', 0, -1);
        }

        // Rebuild the full sequence string for validation
        $reconstructedSequence = $prefix
                                 .$year
                                 .($this->sequence->year_separator ?? '')
                                 .$numberWithoutCheckDigit
                                 .($this->sequence->check_digit_separator ?? '')
                                 .$suffix;

        // Calculate the expected check digit for the reconstructed sequence
        $expectedCheckDigit = $this->calculateCheckDigit($reconstructedSequence);

        // Compare the actual and expected check digits
        return $actualCheckDigit === (string) $expectedCheckDigit;
    }
}

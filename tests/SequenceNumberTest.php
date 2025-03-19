<?php

use Ameax\SequenceNumber\Models\Sequence;
use Ameax\SequenceNumber\Services\SequenceService;
use Ameax\SequenceNumber\Tests\TestCase;

it('it generates a sequence number', function () {
    /** @var $this TestCase */
    $token = 'invoice';

    /** @var Sequence $sequence */
    $sequence = Sequence::create([
        'token' => $token,
        'prefix' => 'INV',
        'number_min_length' => null,
    ]);

    expect(SequenceService::make()->byToken('invoice')->next())->toBe('INV1');

    $sequence->number_min_length = 3;
    $sequence->save();
    expect(SequenceService::make()->byToken('invoice')->next())->toBe('INV002');

    $sequence->prefix = 'INV-';
    $sequence->save();
    expect(SequenceService::make()->byToken('invoice')->next())->toBe('INV-003');

    $sequence->year_format = '4-digits';
    $sequence->year_separator = '.';
    $sequence->save();

    $year = now()->year;
    expect(SequenceService::make()->byToken('invoice')->next())->toBe('INV-'.$year.'.004');

    $sequence->suffix = '-TEST';
    $sequence->save();
    expect(SequenceService::make()->byToken('invoice')->next())->toBe('INV-'.$year.'.005-TEST');

    // Make sequence from model
    expect($sequence->next())->toBe('INV-'.$year.'.006-TEST');

    $sequence->delete();

    $sequence = Sequence::create([
        'token' => $token,
        'prefix' => 'IV',
        'use_check_digit' => true,
        'check_digit_position' => 'prefix',
        'check_digit_separator' => '-',
    ]);
    expect(SequenceService::make()->byToken('invoice')->next())->toBe('IV0-1');
    expect(SequenceService::make()->byToken('invoice')->next())->toBe('IV8-2');
    expect(SequenceService::make()->byToken('invoice')->next())->toBe('IV6-3');
    expect(SequenceService::make()->byToken('invoice')->next())->toBe('IV4-4');
    expect(SequenceService::make()->byToken('invoice')->validateSequenceNumber('IV4-4'))->toBeTrue();
    expect(SequenceService::make()->byToken('invoice')->validateSequenceNumber('iv44'))->toBeTrue();
    expect(SequenceService::make()->byToken('invoice')->validateSequenceNumber('IV2-4'))->toBeFalse();

    // Todo check year sequence and start_value

});

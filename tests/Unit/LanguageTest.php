<?php

use App\Enums\Language;

it('can get an associated language label', function () {
    expect(Language::from("en")->getLabel())->toBe('English');
});
<?php

test('can hide full name', function () {
    $name = 'Rakib Ahmed';
    $result = maskFullName($name);

    expect($result)->toBe('R**** A****');
});

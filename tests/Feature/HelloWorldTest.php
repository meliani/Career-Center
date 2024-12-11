<?php

it('returns hello world', function () {
    $response = 'hello world';
    expect($response)->toBe('hello world');
});
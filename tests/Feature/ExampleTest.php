<?php

test('the application returns a redirect response to login', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});

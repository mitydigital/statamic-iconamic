<?php

use MityDigital\Iconamic\Fieldtypes\Iconamic as IconamicFieldtype;
use Statamic\Facades\User;

beforeEach(function () {
    // The control panel encrypts its cookies, so these requests need an app key.
    config(['app.key' => 'base64:'.base64_encode(random_bytes(32))]);

    $this->url = (new IconamicFieldtype)->preload()['url'];
    $this->user = User::make()->id('editor')->email('editor@example.com')->makeSuper();
});

it('returns the icon list for the url the fieldtype preloads', function () {
    $this->actingAs($this->user)
        ->getJson($this->url)
        ->assertOk()
        ->assertJsonCount(5, 'icons')
        ->assertHeader('Cache-Control', 'max-age=3600, private');
});

it('rejects a url whose folder has been changed', function () {
    $this->actingAs($this->user)
        ->getJson(str_replace('path=svg', 'path=..', $this->url))
        ->assertNotFound();
});

it('rejects a url without a signature', function () {
    $this->actingAs($this->user)
        ->getJson(strtok($this->url, '?').'?path=svg&path_helper=base_path&recursive=default')
        ->assertNotFound();
});

it('requires a control panel login', function () {
    $this->get($this->url)->assertRedirect();
});

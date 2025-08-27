<?php

use MityDigital\Iconamic\Tags\Iconamic;
use Statamic\Fields\Field;
use Statamic\Fields\Value;

beforeEach(function () {
    $this->tag = new Iconamic;
});

it('returns nothing with no params or config', function () {
    $this->assertEquals('', $this->tag->index());
});

it('can return svg of icon with index', function () {
    // create the field
    $field = new Field(
        'icon',
        [
            'path' => 'svg',
            'path_helper' => 'default',
        ]
    );

    // create the fieldtype
    $fieldtype = new \MityDigital\Iconamic\Fieldtypes\Iconamic;
    $fieldtype->setField($field);

    // set the required context
    $this->tag->setContext([
        'icon' => new Value('square', 'icon', $fieldtype),
    ]);
    $this->tag->setParameters([]);

    $this->assertEquals(getSvgSource('square', 1), $this->tag->index());
});

it('returns nothing when icon not found', function () {
    // create the field
    $field = new Field(
        'icon',
        [
            'path' => 'svg',
            'path_helper' => 'default',
        ]
    );

    // create the fieldtype
    $fieldtype = new \MityDigital\Iconamic\Fieldtypes\Iconamic;
    $fieldtype->setField($field);

    // set the required context
    $this->tag->setContext([
        'icon' => new Value('icon-that-doesnt-exist', 'icon', $fieldtype),
    ]);
    $this->tag->setParameters([]);

    $this->assertEquals('', $this->tag->index());
});

it('can return true when icon exists using has', function () {
    // create the field
    $field = new Field(
        'icon',
        [
            'path' => 'svg',
            'path_helper' => 'default',
        ]
    );

    // create the fieldtype
    $fieldtype = new \MityDigital\Iconamic\Fieldtypes\Iconamic;
    $fieldtype->setField($field);

    // set the required context
    $this->tag->setContext([
        'icon' => new Value('triangle', 'icon', $fieldtype),
    ]);
    $this->tag->setParameters([]);

    $this->assertTrue($this->tag->has());
});

it('can return false when icon does not exist using has', function () {
    // create the field
    $field = new Field(
        'icon',
        [
            'path' => 'svg',
            'path_helper' => 'default',
        ]
    );

    // create the fieldtype
    $fieldtype = new \MityDigital\Iconamic\Fieldtypes\Iconamic;
    $fieldtype->setField($field);

    // set the required context
    $this->tag->setContext([
        'icon' => new Value('icon-that-doesnt-exist', 'icon', $fieldtype),
    ]);
    $this->tag->setParameters([]);

    $this->assertFalse($this->tag->has());
});

it('can return svg of icon with wildcard', function () {
    // create the field
    $field = new Field(
        'icon',
        [
            'path' => 'svg',
            'path_helper' => 'default',
        ]
    );

    // create the fieldtype
    $fieldtype = new \MityDigital\Iconamic\Fieldtypes\Iconamic;
    $fieldtype->setField($field);

    // set the required context
    $this->tag->setContext([
        'custom_icon' => new Value('circle', 'custom_icon', $fieldtype),
    ]);
    $this->tag->setParameters([]);

    $this->assertEquals(getSvgSource('circle', 1), $this->tag->wildcard('custom_icon'));
});

it('returns nothing from wildcard if handle not found', function () {
    // create the field
    $field = new Field(
        'icon',
        [
            'path' => 'svg',
            'path_helper' => 'default',
        ]
    );

    // create the fieldtype
    $fieldtype = new \MityDigital\Iconamic\Fieldtypes\Iconamic;
    $fieldtype->setField($field);

    // set the required context
    $this->tag->setContext([
        'custom_icon' => new Value('circle', 'custom_icon', $fieldtype),
    ]);
    $this->tag->setParameters([]);

    $this->assertEquals('', $this->tag->wildcard('fake_handle'));
});

it('includes additional icons in returned icon', function () {
    // create the field
    $field = new Field(
        'icon',
        [
            'path' => 'svg',
            'path_helper' => 'default',
        ]
    );

    // create the fieldtype
    $fieldtype = new \MityDigital\Iconamic\Fieldtypes\Iconamic;
    $fieldtype->setField($field);

    // set the required context
    $this->tag->setContext([
        'icon' => new Value('simple', 'icon', $fieldtype),
    ]);
    $this->tag->setParameters([
        'class' => 'w-4 h-4',
        'mity' => 'digital',
    ]);

    $this->assertEquals(
        '<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" class="w-4 h-4" mity="digital"><ellipse stroke-width="0" stroke="#fff" ry="5" rx="5" id="iconamic-1-svg_1" cy="5" cx="5" fill="#000"/></svg>',
        $this->tag->index());
});

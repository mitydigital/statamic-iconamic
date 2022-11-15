<?php

namespace MityDigital\Iconamic\Tests\Support;

use MityDigital\Iconamic\Tests\TestCase;
use MityDigital\Iconamic\Fieldtypes\Iconamic as IconamicFieldtype;
use Statamic\Fields\Field;

class IconamicFieldtypeTest extends TestCase
{
    protected IconamicFieldtype $fieldtype;

    public function setUp(): void
    {
        parent::setUp();

        $this->fieldtype = new IconamicFieldtype();
    }

    /** @test */
    public function has_path_and_path_helper_config_fields()
    {
        $fields = $this->fieldtype->configFields();

        // have the required fields
        $this->assertEquals(
            [
                'path',
                'path_helper',
                'recursive'
            ],
            $fields->values()->keys()->toArray()
        );

        // path is type text
        $this->assertEquals('text', $fields->get('path')->config()['type']);

        // path helper is a select with the valid options
        $this->assertEquals('select', $fields->get('path_helper')->config()['type']);
        $this->assertEquals([
            'default',
            'app_path',
            'base_path',
            'public_path',
            'resource_path',
            'storage_path'
        ], array_keys($fields->get('path_helper')->config()['options']));
    }

    /** @test */
    public function preload_gets_list_of_icons_using_defaults()
    {
        $icons = $this->fieldtype->preload();

        $this->assertArrayHasKey('icons', $icons);
        $this->assertCount(5, $icons['icons']);
    }

    /** @test */
    public function preload_gets_list_of_icons_using_field_overrides()
    {
        $field = new Field(
            'icon',
            [
                'path'        => 'icon-svg',
                'path_helper' => 'resource_path'
            ]
        );

        $this->fieldtype->setField($field);

        $icons = $this->fieldtype->preload();

        $this->assertArrayHasKey('icons', $icons);
        $this->assertCount(3, $icons['icons']);
    }
}

<?php

namespace MityDigital\Iconamic\Tests\Tags;

use MityDigital\Iconamic\Tags\Iconamic;
use MityDigital\Iconamic\Tests\TestCase;
use Statamic\Fields\Field;
use Statamic\Fields\Value;

class IconamicTagTest extends TestCase
{
    protected Iconamic $tag;

    public function setUp(): void
    {
        parent::setUp();

        $this->tag = new Iconamic();
    }

    /** @test */
    public function index_returns_nothing_with_no_params_or_config()
    {
        $this->assertEquals('', $this->tag->index());
    }

    /** @test */
    public function index_returns_svg_of_icon()
    {
        // create the field
        $field = new Field(
            'icon',
            [
                'path' => 'svg',
                'path_helper' => 'default',
            ]
        );

        // create the fieldtype
        $fieldtype = new \MityDigital\Iconamic\Fieldtypes\Iconamic();
        $fieldtype->setField($field);

        // set the required context
        $this->tag->setContext([
            'icon' => new Value('square', 'icon', $fieldtype),
        ]);
        $this->tag->setParameters([]);

        $this->assertEquals($this->getSvgSource('square', 1), $this->tag->index());
    }

    /** @test */
    public function index_returns_nothing_when_icon_not_found()
    {
        // create the field
        $field = new Field(
            'icon',
            [
                'path' => 'svg',
                'path_helper' => 'default',
            ]
        );

        // create the fieldtype
        $fieldtype = new \MityDigital\Iconamic\Fieldtypes\Iconamic();
        $fieldtype->setField($field);

        // set the required context
        $this->tag->setContext([
            'icon' => new Value('icon-that-doesnt-exist', 'icon', $fieldtype),
        ]);
        $this->tag->setParameters([]);

        $this->assertEquals('', $this->tag->index());
    }

    /** @test */
    public function has_returns_true_if_icon_exists()
    {
        // create the field
        $field = new Field(
            'icon',
            [
                'path' => 'svg',
                'path_helper' => 'default',
            ]
        );

        // create the fieldtype
        $fieldtype = new \MityDigital\Iconamic\Fieldtypes\Iconamic();
        $fieldtype->setField($field);

        // set the required context
        $this->tag->setContext([
            'icon' => new Value('triangle', 'icon', $fieldtype),
        ]);
        $this->tag->setParameters([]);

        $this->assertTrue($this->tag->has());
    }

    /** @test */
    public function has_returns_false_if_icon_does_not_exist()
    {
        // create the field
        $field = new Field(
            'icon',
            [
                'path' => 'svg',
                'path_helper' => 'default',
            ]
        );

        // create the fieldtype
        $fieldtype = new \MityDigital\Iconamic\Fieldtypes\Iconamic();
        $fieldtype->setField($field);

        // set the required context
        $this->tag->setContext([
            'icon' => new Value('icon-that-doesnt-exist', 'icon', $fieldtype),
        ]);
        $this->tag->setParameters([]);

        $this->assertFalse($this->tag->has());
    }

    /** @test */
    public function wildcard_returns_svg_of_icon()
    {
        // create the field
        $field = new Field(
            'icon',
            [
                'path' => 'svg',
                'path_helper' => 'default',
            ]
        );

        // create the fieldtype
        $fieldtype = new \MityDigital\Iconamic\Fieldtypes\Iconamic();
        $fieldtype->setField($field);

        // set the required context
        $this->tag->setContext([
            'custom_icon' => new Value('circle', 'custom_icon', $fieldtype),
        ]);
        $this->tag->setParameters([]);

        $this->assertEquals($this->getSvgSource('circle', 1), $this->tag->wildcard('custom_icon'));
    }

    /** @test */
    public function wildcard_returns_nothing_if_handle_not_found()
    {
        // create the field
        $field = new Field(
            'icon',
            [
                'path' => 'svg',
                'path_helper' => 'default',
            ]
        );

        // create the fieldtype
        $fieldtype = new \MityDigital\Iconamic\Fieldtypes\Iconamic();
        $fieldtype->setField($field);

        // set the required context
        $this->tag->setContext([
            'custom_icon' => new Value('circle', 'custom_icon', $fieldtype),
        ]);
        $this->tag->setParameters([]);

        $this->assertEquals('', $this->tag->wildcard('fake_handle'));
    }

    /** @test */
    public function returned_icon_includes_additional_attributes()
    {
        // create the field
        $field = new Field(
            'icon',
            [
                'path' => 'svg',
                'path_helper' => 'default',
            ]
        );

        // create the fieldtype
        $fieldtype = new \MityDigital\Iconamic\Fieldtypes\Iconamic();
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
    }
}

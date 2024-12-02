<?php

namespace TWithers\IconsPlus\Fieldtypes;

use Illuminate\Support\Facades\Http;
use Statamic\Fields\Fieldtype;

class IconsPlus extends Fieldtype
{
    public $categories = ['media'];
    /**
     * The blank/default value.
     *
     * @return array
     */
    public function defaultValue()
    {
        return null;
    }

    public function icon(): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"><path stroke-linejoin="round" d="M21.25 13V8.5a5 5 0 0 0-5-5h-8.5a5 5 0 0 0-5 5v7a5 5 0 0 0 5 5h6.26"/><path stroke-linejoin="round" d="m3.01 17l2.74-3.2a2.2 2.2 0 0 1 2.77-.27a2.2 2.2 0 0 0 2.77-.27l2.33-2.33a4 4 0 0 1 5.16-.43l2.47 1.91M8.01 10.17a1.66 1.66 0 1 0-.02-3.32a1.66 1.66 0 0 0 .02 3.32"/><path stroke-miterlimit="10" d="M18.994 15.5v5M16.5 18.005h5"/></g></svg>';
    }

    /**
     * Pre-process the data before it gets sent to the publish page.
     *
     * @param mixed $data
     * @return array|mixed
     */
    public function preProcess($data)
    {
        return $data;
    }

    /**
     * Process the data before it gets saved.
     *
     * @param mixed $data
     * @return array|mixed
     */
    public function process($data)
    {
        return $data;
    }

    protected function configFieldItems(): array
    {
        $sets = collect(Http::get('https://api.iconify.design/collections')->json())
            ->mapWithKeys(fn ($collection, $key) => [$key => $collection['name']]);

        return [
            'storage_options' => [
                'display' => 'Storage Options',
                'instructions' => 'Choose the icon data you want stored.',
                'type' => 'select',
                'options' => [
                    'both' => 'Both URL and SVG',
                    'url' => 'Icon\'s remote URL only',
                    'svg' => 'Full icon SVG only',
                ],
                'default' => 'both',
            ],
            'icon_group_restrictions' => [
                'display' => 'Icon Group Restrictions',
                'instructions' => 'Restrict icons to a specific group(s).',
                'type' => 'select',
                'default' => [],
                'multiple' => true,
                'options' => $sets->sortKeys()->toArray(),
            ],
        ];
    }
}

<?php

namespace Twithers\IconsPlus\Tags;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class IconTag extends \Statamic\Tags\Tags
{
    protected static $handle = 'icons_plus';

    protected static $aliases = ['ip'];

    protected ?string $svgData = null;

    protected ?string $imgData = null;

    public function index(): ?string
    {
        if (! empty($this->params->get('icon'))) {
            if (! empty($this->params->get('icon')['iconSvg'])) {
                $this->svgData = $this->params->get('icon')['iconSvg'];

                return $this->svg();
            } elseif (! empty($this->params->get('icon')['label'])) {
                $this->imgData = $this->params->get('icon')['label'];

                return $this->img();
            }

            if (is_string($this->params->get('icon')) && str_contains($this->params->get('icon'), ':')) {
                $this->imgData = $this->params->get('icon');

                return $this->img();
            }
        }

        if ($this->context->get('iconSvg') && ! empty($this->context->get('iconSvg'))) {
            $this->svgData = $this->context->get('iconSvg');

            return $this->svg();
        } elseif ($this->context->get('label') && ! empty($this->context->get('label'))) {
            $this->imgData = $this->context->get('label');

            return $this->img();
        }

        return null;
    }

    public function svg(): ?string
    {
        if ($this->svgData === null) {
            $this->svgData = $this->params->get('icon')['iconSvg'] ?? $this->params->get('svg');
        }

        if (! $this->svgData) {
            return null;
        }

        if (! preg_match('/^<svg[^>]*>/', $this->svgData)) {
            return null;
        }

        return $this->renderSVG();
    }

    public function img(): ?string
    {
        [$collection, $name] = explode(':', $this->imgData);

        $this->svgData = Cache::remember('icons_plus_'.$collection.'_'.$name, 60 * 60 * 24, function () use ($collection, $name) {
            $response = Http::get('https://api.iconify.design/'.$collection.'/'.$name.'.svg?width=unset&height=unset');

            if ($response->failed()) {
                return null;
            }

            return $response->body();
        });

        return $this->renderSVG();
    }

    public function wildcard($iconName): ?string
    {
        $this->imgData = $iconName;

        return $this->img();
    }

    protected function getAttributes(): string
    {
        return $this->params->except(['svg', 'img', 'icon'])->map(function ($value, $attribute) {
            return "{$attribute}=\"{$value}\"";
        })->implode(' ');
    }

    protected function renderSVG(): ?string
    {
        return preg_replace('/^<svg([^>]*)>/', '<svg$1 '.$this->getAttributes().'>', $this->svgData);
    }
}

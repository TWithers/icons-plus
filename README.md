# Icons Plus for Statamic

> Icons Plus is a fieldtype that leverages https://iconify.design to provide a searchable list of open source icons for use in your Statamic site.
> Icons are stored both as a URL and SVG so they can be used as desired in templates.

## Features

- Super easy to install and use
- Provides some configuration options for the fieldtype including icon library restrictions

## How to Install

You can install this addon via Composer:

``` bash
composer require twithers/icons-plus
```

## How to Use

After adding the fieldtype to your blueprint, you can then store a selected icon.
 
The stored icon has the following keys:

#### iconPrefix
The prefix of the icon. This is used to identify the icon library.

#### iconName
The name of the icon. This is used to identify the specific icon within the library.

#### label
The prefix + name of the icon, separated by a `:`.

#### iconUrl
The URL of the icon. This is the URL to the SVG file. This allows for use in `<img src="{{ iconUrl }}" />`.

#### iconSvg
The SVG of the icon. This is the SVG markup for the icon. This allows for use in `{!! iconSvg !!}`.

## Usage in Templates
The tag `{{ icons_plus }}` is available to use in your templates.
It can be used as context or as a tag pair.

The alias `{{ ip }}` is also available for the tag.

### Context
``` twig
{{ selected_icons_plus_icon }}
    {{ icons_plus }}
{{ /selected_icons_plus_icon }}
```
The tag will resort to the stored SVG first, and fall back on fetching the icon svg via cached http request.


### As a standalone tag
``` twig
{{ icons_plus :icon="selected_icons_plus_icon" }}
```

### Wildcard support
You can also use the icon's collection and name to render an icon via API:
``` twig
{{ icons_plus:heroicons:chart-pie }}
```

### Attibute support
All attributes are supported:
``` twig
{{ icons_plus :icon="selected_icons_plus_icon" class="w-6 h-6 text-blue-500" x-data="" @click="console.log('clicked')" }}
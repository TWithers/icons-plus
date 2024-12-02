# Icons Plus

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

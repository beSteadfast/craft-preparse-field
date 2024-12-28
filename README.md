# Preparse Field for Craft

A fieldtype that parses Twig when an element is saved and saves the result as plain text.

## Requirements

This plugin requires Craft CMS 4.0.0 or later and PHP 8.0.2 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require jalendport/craft-preparse

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Preparse Field.

## Usage

When creating a new Preparse field, you add the Twig that you want run to the field's settings. When an element with that Preparse field is saved, the code will be parsed and the resulting value saved as plain text.

It's worth noting that the Preparse field is only updated when the element the field is on is saved. If you grab data from a related element (like in the category title example below), and then update the related element, the preparsed value will not automatically be updated.

In the Twig, the element that the Preparse field is added to is available as a variable named `element`. It's best to use this variable (as opposed to something like `entry` or `asset`) because it's possible you add the same Preparse field to multiple element types. This also means that when a Preparse field is added to a Matrix, SuperTable, or Neo block, that block will be what is available as `element`, so if you want to access the element that the Matrix/SuperTable/Neo field belongs to, you will want to use `element.owner`.

### Examples

If you have a category field on your element named `relatedCategory`, you can save the category title to the Preparse field by adding the following Twig to the field settings:

    {{ element.relatedCategory.one().title ?? '' }}

This is useful for saving preparsed values to a field for use with sorting, searching, or similar things.

You can also do more advanced stuff, for instance performance optimizing. Let's say you have three different asset fields that may or may not be populated. Having to check these in the template may require a bunch of queries since you can't check if a field has a relation in Craft without actually querying for it. You could do something like this to get the id of the asset to use:

    {% if element.smallListImage | length %}
        {{ element.smallListImage.one().id }}
    {% elseif element.largeListImage | length %}
        {{ element.largeListImage.one().id }}
    {% elseif element.mainImage | length %}
        {{ element.mainImage.one().id }}
    {% endif %}

_You'd probably want to wrap that in `{% apply spaceless %} ... {% endapply %}` to make it more useful..._

Or you could just use it to do some bulk work when saving, like pre-generating a bunch of image transforms with [Imager X](https://plugins.craftcms.com/imager-x?craft4):

    {% if element.mainImage | length %}
        {% set transformedImages = craft.imager.transformImage(element.mainImage.one(), [
        { width: 1000 },
            { width: 900 },
            { width: 800 },
            { width: 700 },
            { width: 600 },
            { width: 500 },
            { width: 400 },
            { width: 300 },
            { width: 200 },
            { width: 100 }
        ]) %}
    {% endif %}

Preparse also has access to your site's template root, so you can even include local templates if you want to do more advanced stuff and/or want to keep your field's Twig in version control:

    {% include '_partials/customPreparseFieldStuff' %}

Make sure that you always write solid Twig, taking into account that fields may not be populated yet. If an error occurs in your Twig, the element will not be saved. [Code defensively!](https://nystudio107.com/blog/handling-errors-gracefully-in-craft-cms#defensive-coding-in-twig)

## Price, License, and Support

The plugin is released under the MIT license, meaning you can do whatever you want with it as long as you don't blame us. **It's free**, which means there is absolutely no support included, but you might get it anyway. Just post an issue here on GitHub if you have one, and we'll see what we can do. :)

## Changelog

See the [changelog file](https://github.com/jalendport/craft-preparse/blob/master/CHANGELOG.md).

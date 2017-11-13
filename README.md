# Translate Tabs plugin
Plugin adds possibility to display translatable fields grouped into tabs by locales using translations formwidget.

## Requirements
#### RainLab.Translate plugin

## Usage
In model class you need to add 'Vdomah.TranslateTabs.Behaviors.TranslateTabbable' behavior to implement array
besides TranslatableModel behavior. Then define translatable attributes as you would do usually with Translate plugin.

    public $implement = [
        'RainLab.Translate.Behaviors.TranslatableModel',
        'Vdomah.TranslateTabs.Behaviors.TranslateTabbable',
    ];

    public $translatable = ['name', 'excerpt', 'slug'];

In fields.yaml need to define _translations field like this:

    _translations:
        span: left
        type: translations
        form:
            fields:
                name:
                    label: Name
                    span: auto
                    type: text
                slug:
                    label: Slug
                    span: auto
                    preset:
                        field: name
                        type: slug
                    type: text
                excerpt:
                    label: Excerpt
                    span: auto
                    type: textarea
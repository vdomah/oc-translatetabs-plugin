# Translate Tabs plugin
Plugin adds possibility to display translatable fields grouped by tabs.

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
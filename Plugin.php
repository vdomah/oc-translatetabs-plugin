<?php namespace Vdomah\TranslateTabs;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public $require = ['RainLab.Translate'];

    public function registerComponents()
    {
    }

    public function registerSettings()
    {
    }

    public function registerFormWidgets()
    {
        return [
            'Vdomah\TranslateTabs\FormWidgets\Translations' => [
                'label' => 'Translations',
                'code'  => 'translations'
            ],
        ];
    }
}

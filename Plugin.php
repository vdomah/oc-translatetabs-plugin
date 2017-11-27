<?php namespace Vdomah\TranslateTabs;

use Cms\Models\ThemeData;
use System\Classes\PluginBase;
use Cms\Classes\Theme;

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

    public function boot()
    {
        ThemeData::extend(function($model) {
            $theme = Theme::getActiveTheme();
            $form = $theme->getConfigValue('form');
            if (is_array($form)) {
                $translations = $this->findThemeTrans($form);

                if (is_array($translations)) {
                    $model->implement[] = 'RainLab.Translate.Behaviors.TranslatableModel';
                    $model->implement[] = 'Vdomah.TranslateTabs.Behaviors.TranslateTabbable';
                    $model->addDynamicProperty('translatable', array_keys($translations));
                }
            }
        });
    }

    public function findThemeTrans($arr)
    {
        if (!count($arr))
            return false;

        if ($translations = array_get($arr, '_translations')) {
            return array_get($translations, 'form.fields');
        } else {
            if (is_array($arr))
                foreach ($arr as $item) {
                    if ($_trans = $this->findThemeTrans($item))
                        return $_trans;
                }
            else
                return false;
        }
    }
}
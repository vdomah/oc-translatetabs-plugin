<?php namespace Vdomah\TranslateTabs\Behaviors;

use App;
use October\Rain\Extension\ExtensionBase;
use RainLab\Translate\Models\Locale as LocaleModel;

class TranslateTabbable extends ExtensionBase
{
    /**
     * @var \October\Rain\Database\Model Reference to the extended model.
     */
    protected $model;

    /**
     * Constructor
     * @param \October\Rain\Database\Model $model The extended model.
     */
    public function __construct($model)
    {
        $this->model = $model;

        $this->model->bindEvent('model.beforeSave', function() {
            if (App::runningInBackend()) {
                foreach (LocaleModel::listEnabled() as $locale=>$lang) {
                    if ($locale_data = post($locale)) {
                        if (get_class($this->model) == 'Cms\Models\ThemeData') {
                            $translatable = array_keys($locale_data);
                            $this->model->translatable = $translatable;
                        } else {
                            $translatable = $this->model->translatable;
                        }
                        foreach ($translatable as $attr) {
                            if (isset($locale_data[$attr])) {
                                $this->model->setAttributeTranslated($attr, $locale_data[$attr], $locale);
                            }
                        }
                    }
                }

                $this->model->syncTranslatableAttributes();

                if (get_class($this->model) == 'Cms\Models\ThemeData') {
                    unset($this->model->translatable);
                }
            }
        });
    }
}
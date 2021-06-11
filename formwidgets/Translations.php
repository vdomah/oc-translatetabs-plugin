<?php namespace Vdomah\TranslateTabs\FormWidgets;

use Backend\Classes\FormWidgetBase;
use RainLab\Translate\Models\Locale as LocaleModel;
use Backend\Classes\FormField;

/**
 * Widget to create set of translatable fields grouped into language tabs.
 *
 * @package vdomah\translatetabs
 * @author Art Gek
 */
class Translations extends FormWidgetBase
{

    /**
     * @var array Form field configuration
     */
    public $form;

    /**
     * {@inheritDoc}
     */
    protected $defaultAlias = 'translations';

    /**
     * @var array Collection of form widgets.
     */
    protected $formWidgets = [];

    public $tabs = [];

    public $previewMode = false;

    public $viewPathBackend;

    public function init()
    {
        $this->fillFromConfig([
            'form',
        ]);

        $this->bindToController();

        $this->viewPathBackend = base_path() . '/modules/backend/widgets/form/partials/';
        $this->viewPathWidget = 'vdomah/translatetabs/formwidgets/translations/partials/';

        $this->makeItemFormWidget();
    }

    public function render()
    {
        $this->prepareVars();
        return $this->makePartial($this->viewPathWidget . 'default');
    }

    /**
     * Prepares the form widget view data
     */
    public function prepareVars()
    {
        $this->vars['model'] = $this->model;
        $this->vars['locale'] = $this->formField->attributes['field']['locale'];
    }

    protected function processExistingItems()
    {
        $loadValue = $this->getLoadValue();
        if (is_array($loadValue)) {
            $loadValue = array_keys($loadValue);
        }

        $itemIndexes = post($this->formField->getName(false), $loadValue);

        if (!is_array($itemIndexes)) {
            return;
        }

        foreach ($itemIndexes as $itemIndex) {
            $this->makeItemFormWidget($itemIndex);
            $this->indexCount = max((int) $itemIndex, $this->indexCount);
        }
    }

    protected function makeItemFormWidget($index = 0)
    {
        $loadValue = $this->getLoadValue();
        if (!is_array($loadValue)) {
            $loadValue = [];
        }

        $config = $this->makeConfig($this->form);//dd($config->fields);
        $config->model = $this->model;
        $config->data = array_get($loadValue, $index, []);
        $config->alias = $this->alias . 'Form'.$index;
        $config->arrayName = $this->getFieldName().'['.$index.']';
        $config->isNested = true;

        $widget = $this->makeWidget('Backend\Widgets\Form', $config);
        $widget->bindToController();

        foreach (LocaleModel::listEnabled() as $locale=>$lang) {
            $fields = [];
            $this->tabs[$locale]['label'] = $lang;

            foreach ($config->fields as $field_name=>$field_config) {
                $label = (isset($field_config['label'])) ? $field_config['label'] : null;

                $fieldObj = new FormField($field_name, $label);

                if (isset($field_config['cssClass'])) {
                    $fieldObj->cssClass = $field_config['cssClass'];
                }
                if (isset($field_config['span'])) {
                    $fieldObj->span = $field_config['span'];
                }
                $fieldObj->arrayName = $locale;

                $fieldType = isset($field_config['type']) ? $field_config['type'] : null;
                if (!in_array($fieldType, ['text', 'textarea']))
                    continue;
                if (!is_string($fieldType) && !is_null($fieldType)) {
                    throw new ApplicationException(Lang::get(
                        'backend::lang.field.invalid_type',
                        ['type'=>gettype($fieldType)]
                    ));
                }

                $fieldObj->displayAs($fieldType, $field_config);
                $fieldObj->value = $this->model->getAttributeTranslated($fieldObj->fieldName, $locale);

                $fields[] = $fieldObj;
            }

            $this->tabs[$locale]['fields'] = $fields;
        }

        return $this->formWidgets[$index] = $widget;
    }

    /**
     * Renders the HTML element for a field
     */
    public function renderFieldElement($field)
    {
        return $this->makePartial(
            $this->viewPathBackend . 'field_' . $field->type,
            [
                'field' => $field,
                'formModel' => $this->model
            ]
        );
    }

    public function renderFieldTrans($locale, $fieldName, $options = [])
    {
        if (!isset($options['type']))
            $options['type'] = 'text';

        if (!isset($options['label']))
            $options['label'] = ucfirst($fieldName);

        $field = new FormField('Law[' . $locale . '][' . $fieldName . ']', $options['label'] . ' ' . $locale);

        if (isset($options['widget'])) {
            $widgetConfig = $this->makeConfig($field->config);
            $widgetConfig->previewMode = $this->previewMode;
            $widgetConfig->model = $this->model->getTranslationByLocale($locale);
            $widgetConfig->data = $widgetConfig->model->content_html;

            $widget = $this->makeFormWidget($options['type'], $field, $widgetConfig);
            $field->displayAs('widget', ['widget' => $widgetConfig]);
            $widget->prepareVars();
            $widget->vars['value'] = htmlentities($widgetConfig->data, ENT_QUOTES, 'UTF-8', true);

            return $widget->makePartial('codeeditor');
        } else {
            $field->displayAs($options['type']);
            $field->value = $this->model->getAttributeTranslated($fieldName, $locale);

            return $this->renderFieldElement($field);
        }
    }

    public function showFieldLabels($field)
    {
        return true;
    }

    protected function getFieldDepends($field)
    {
        if (!$field->dependsOn) {
            return '';
        }

        $dependsOn = is_array($field->dependsOn) ? $field->dependsOn : [$field->dependsOn];
        $dependsOn = htmlspecialchars(json_encode($dependsOn), ENT_QUOTES, 'UTF-8');
        return $dependsOn;
    }
}

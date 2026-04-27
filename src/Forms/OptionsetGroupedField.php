<?php

namespace Sunnysideup\OptionsetFieldGrouped\Forms;

use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\SingleLookupField;
use SilverStripe\ORM\ArrayLib;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;

class OptionsetGroupedField extends OptionsetField
{

    protected $schemaDataType = 'OptionsetGroupedField';

    protected $template = OptionsetGroupedField::class;

    /**
     * Build a potentially nested fieldgroup
     *
     * @param mixed $valueOrGroup Value of item, or title of group
     * @param string|array $titleOrOptions Title of item, or options in grouip
     * @return ArrayData Data for this item
     */
    protected function getFieldOption($valueOrGroup, $titleOrOptions, $odd)
    {
        // Return flat option
        if (!is_array($titleOrOptions)) {
            return parent::getFieldOption($valueOrGroup, $titleOrOptions, $odd);
        }

        // Build children from options list
        $options = ArrayList::create();
        foreach ($titleOrOptions as $childValue => $childTitle) {
            $options->push($this->getFieldOption($childValue, $childTitle, $odd));
        }

        return ArrayData::create([
            'Title' => $valueOrGroup,
            'Options' => $options
        ]);
    }

    public function Type()
    {
        return 'optionsetfieldgrouped optionsetfield';
    }

    protected function getSourceValues()
    {
        // Flatten values
        $values = [];
        $source = $this->getSource();
        array_walk_recursive(
            $source,
            // Function to extract value from array key
            function ($title, $value) use (&$values) {
                $values[] = $value;
            }
        );
        return $values;
    }

    /**
     * @return SingleLookupField
     */
    public function performReadonlyTransformation()
    {
        $field = parent::performReadonlyTransformation();
        $field->setSource(ArrayLib::flatten($this->getSource()));
        return $field;
    }
}

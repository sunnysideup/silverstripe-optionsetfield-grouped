<?php

namespace Sunnysideup\OptionsetFieldGrouped\Forms;

use Override;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\Model\ArrayData;
use SilverStripe\Core\ArrayLib;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\SingleLookupField;

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
    #[Override]
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

    #[Override]
    public function Type()
    {
        return 'optionsetfieldgrouped optionsetfield';
    }

    #[Override]
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
    #[Override]
    public function performReadonlyTransformation()
    {
        $field = parent::performReadonlyTransformation();
        $field->setSource(ArrayLib::flatten($this->getSource()));
        return $field;
    }
}

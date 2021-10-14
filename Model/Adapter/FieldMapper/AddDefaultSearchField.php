<?php

namespace Mrl\ESIKAnalyzer\Model\Adapter\FieldMapper;


class AddDefaultSearchField extends \Magento\Elasticsearch\Model\Adapter\FieldMapper\AddDefaultSearchField
{
    /**
     * catch all field name
     */
    private const NAME = '_search';
    /**
     * Add default search field (catch all field) to the fields.
     *
     * Emulates catch all field (_all) for elasticsearch
     *
     * @param array $mapping
     * @return array
     */    
    public function process(array $mapping): array
    {
        return [self::NAME => [
                    'type' => 'text', 
                    'analyzer' => 'ik_max_word']] + $mapping;
    }
}

?>

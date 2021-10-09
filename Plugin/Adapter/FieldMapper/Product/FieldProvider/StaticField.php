<?php
namespace Mrliving\ESIKAnalyzer\Plugin\Adapter\FieldMapper\Product\FieldProvider;


class StaticField
{
    public function afterGetField(
        \Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\FieldProvider\StaticField $subject, 
        $fieldMapping): array
    {
        if ( ! empty($fieldMapping['name'])) {
            $fieldMapping['name']['analyzer'] = 'ik_max_word';
        }

        return $fieldMapping;
    }

    
}

?>
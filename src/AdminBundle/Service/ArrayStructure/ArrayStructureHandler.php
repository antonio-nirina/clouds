<?php
namespace AdminBundle\Service\ArrayStructure;

/**
 * Manipulate array and retrieve customized structured array
 */
class ArrayStructureHandler
{
    /**
     * Redefine array keys using existent key from array element keys
     *
     * @param string $key
     * @param array  $array
     *
     * @return array
     */
    public function redefineKeysBySpecifiedElementKey($key, $array)
    {
        $result = array();
        if (!empty($array)) {
            foreach ($array as $arrayElement) {
                if (array_key_exists($key, $arrayElement)) {
                    $result[$arrayElement[$key]] = $arrayElement;
                }
            }
        }
        return $result;
    }
}

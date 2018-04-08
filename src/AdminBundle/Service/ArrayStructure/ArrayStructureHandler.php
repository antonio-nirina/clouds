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
            foreach ($array as $array_element) {
                if (array_key_exists($key, $array_element)) {
                    $result[$array_element[$key]] = $array_element;
                }
            }
        }
        return $result;
    }
}
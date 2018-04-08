<?php
namespace AdminBundle\Component\CommunicationEmail;

class TemplateSortingParameter
{
    const RECENT = 'recent';
    const A_TO_Z = 'a-to-z';
    const Z_TO_A = 'z-to-a';

    const AVAILABLE_SORTING_PARAMETERS = array(
        null,
        self::RECENT,
        self::A_TO_Z,
        self::Z_TO_A
    );
}

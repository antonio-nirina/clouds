<?php
namespace AdminBundle\Component\SiteForm;

use AdminBundle\Component\SiteForm\FieldType;

class FieldTypeName
{
    const FIELD_NAME = array(
        FieldType::TEXT => "Texte",
        FieldType::ALPHA_TEXT => "Texte alphabétique",
        FieldType::ALPHANUM_TEXT => "Texte alphanumérique",
        FieldType::NUM_TEXT => "Numérique",
        FieldType::EMAIL => "Email",
        FieldType::CHOICE_RADIO => "Bouton radio",
        FieldType::DATE => "Date"
    );
}

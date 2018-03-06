<?php
namespace AdminBundle\Service\Statistique;

use Mailjet\MailjetBundle\Client\MailjetClient ;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
* 
*/
class PdfManager
{
	private $pdf;

   public  function create($orientation = null, $format= null, $lang = null, $unicode = null,$encoding, $margin = null)
   {
    $this->pdf = new \HTML2PDF(
        $orientation ? $orientation : $this->orientation, $format ? $format : $this->format,
        $lang ? $lang : $this->unicode,$unicode ? $encoding : $this->encoding, $margin ? $margin : $this->margin
    );
   }

   public function generatePdf($template, $name)
   {
    $this->pdf->writeHTML($template);
    return $this->pdf->Output($name.'pdf');
   }

}
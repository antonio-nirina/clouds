<?php
namespace AdminBundle\Service\Statistique;



class Common
{
	
	public function getTraitement($listsInfoCampaign,$total=0)
	{
		if (!empty($listsInfoCampaign)) {
	        foreach ($listsInfoCampaign as  $value) {       
                $data["delivre"][]=($value["DeliveredCount"]);
                $data["ouvert"][]=$value["OpenedCount"];
                $data["cliquer"][]=$value["ClickedCount"];
                $data["bloque"][]=$value["BlockedCount"];
                $data["spam"][]=$value["SpamComplaintCount"];
                $data["desabo"][]=$value["UnsubscribedCount"];
                $data["erreur"][]=$value["BouncedCount"]; 
                $data["lastAct"][]=$value["LastActivityAt"];
	        }
		    $delivre=array_sum($data["delivre"]);
	        $ouvert=array_sum($data["ouvert"]);
	        $cliquer=array_sum($data["cliquer"]);
	        $bloque=array_sum($data["bloque"]);
	        $spam=array_sum($data["spam"]);
	        $desabo=array_sum($data["desabo"]);
	        $erreur=array_sum($data["erreur"]);
	        $res["delivre"] = $delivre;
            $res["ouvert"] = $ouvert;
            $res["cliquer"]= $cliquer;
            $res["bloque"] = $bloque;
            $res["spam"] = $spam;
            $res["desabo"] = $desabo;
            $res["erreur"] = $erreur;
            $res["last"] = $data["lastAct"] ;
            $res["total"] = $total;
		}else{
			$res["delivre"] = 0;
            $res["ouvert"] = 0;
            $res["cliquer"]= 0;
            $res["bloque"] = 0;
            $res["spam"] = 0;
            $res["desabo"] = 0;
            $res["erreur"] = 0;
            $res["total"] = $total;
            $res["last"] = new \DateTime(); 
		}

		return $res;

	}
}
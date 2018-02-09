<?php
namespace AdminBundle\Service\Statistique;
use Mailjet\MailjetBundle\Client\MailjetClient ;
use \Mailjet\Resources;


class Common
{
    private $mailjetClient;

    public function __construct(MailjetClient  $mailjetClient)
    {
        $this->mailjetClient=$mailjetClient;
    }
	
	public function getTraitement($listsInfoCampaign)
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
            $res["total"] = $delivre;
            
		}else{
			$res["delivre"] = 0;
            $res["ouvert"] = 0;
            $res["cliquer"]= 0;
            $res["bloque"] = 0;
            $res["spam"] = 0;
            $res["desabo"] = 0;
            $res["erreur"] = 0;
            $res["total"] = 0;
            $res["last"] = new \DateTime(); 
		}

		return $res;

	}

    /**
    * couple sender and to and date and subject
    */ 

    public function getContactByCampaign()
    {
        $campaigns = $this->mailjetClient->get(Resources::$Campaign)->getData();
        if (!empty($campaigns)) {
            foreach ($campaigns as $value) {
            $contacts[]=["sender"=>$value["FromEmail"],
                        "to"=>$value["FromName"],
                        "sujet"=>$value["Subject"],
                        "date"=>$value["CreatedAt"]
                        ];
            }
            return $contacts;
        }
        return "";
        
      
    }

    public function getContactByPeriode($filtre)
    {
        $date=new \DateTime();
        if ($filtre=="Yesterday") {
            $date->modify("-1 day");
            $format= $date->format("Y-m-d");
            $yest = $date->settime(0,0,0)->getTimestamp();
            $filters = ["fromts"=>(string)$yest];
            $campaigns = $this->mailjetClient->get(Resources::$Campaign,['filters' =>$filters])->getData();
            if (!empty($campaigns)) {
                foreach ($campaigns as $value) {
                    $dateFiter = new \DateTime($value["CreatedAt"]);
                    $time= $dateFiter->format("Y-m-d");
                    if ($time == $format) {
                        $contacts[] = [
                        "sender" => $value["FromEmail"],
                        "to" => $value["FromName"],
                        "sujet" => $value["Subject"],
                        "date" => $value["CreatedAt"]
                            ];
                    }
                 
                }
                return $contacts;
            }
        }
        return "";
    }



}
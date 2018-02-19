<?php
namespace AdminBundle\Service\Statistique;
use Mailjet\MailjetBundle\Client\MailjetClient ;
use Symfony\Component\HttpFoundation\JsonResponse;
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
                $data["delivre"][]=$value["DeliveredCount"];
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
            $json = new JsonResponse($listsInfoCampaign);
		}else{
			$res["delivre"] = 0;
            $res["ouvert"] = 0;
            $res["cliquer"]= 0;
            $res["bloque"] = 0;
            $res["spam"] = 0;
            $res["desabo"] = 0;
            $res["erreur"] = 0;
            $res["total"] = 0;
            $res["LastActivityAt"] = new \DateTime(); 
            $json = new JsonResponse($res);
		}
        $results = ["res"=>$res,"json"=>$json];
		return $results;

	}

    /**
    * couple sender and to and date and subject
    */ 

    public function getContactByCampaign()
    {
        $campaigns = $this->mailjetClient->get(Resources::$Campaign)->getData();
        if (!empty($campaigns)) {
                foreach ($campaigns as  $value) {
                    $idCampaign[] = $value["ID"];
                }
                foreach ($idCampaign as  $idData) {
                    $filters = ["campaign"=>$idData];
                    $idConts[$idData] = $this->mailjetClient->get(Resources::$Contactdata,['filters' =>$filters])->getData();
                    foreach ($idConts[$idData]  as  $idCont) {
                    $filters = ["id"=>$idCont["ContactID"]];
                    $allContacts[] = ["idCamp" => $idData,"contact"=>$this->mailjetClient->get(Resources::$Contact,['filters' =>$filters])->getData()];
                       
                    }
                }
                foreach ($allContacts as  $listCont) {
                  
                  $list[] = [$listCont["idCamp"], $listCont["contact"][0]["Email"]];
                }

                foreach ($list as  $res) {
                    $filters = ["id"=>$res[0]];
                    $prods[] = ["dest"=>$res[1],"result"=>$this->mailjetClient->get(Resources::$Campaign,['filters' =>$filters])->getData()[0]];                                      
                }
                foreach ($prods as  $prod) {
                $contacts[] = [
                    "sender" => $prod["result"]["FromEmail"],
                    "sujet" => $prod["result"]["Subject"],
                    "date" => $prod["result"]["CreatedAt"],
                    "emailTo" =>$prod["dest"]
                    ];
                
                }
                return $contacts;
        }
        return "";      
    }

    public function getContactByPeriode($filtre)
    {
        if ($filtre == "Yesterday") {
            $date = new \DateTime();
            $date->modify("-1 day");
            $format= $date->format("Y-m-d");
            $campaigns = $this->getResult($date);        
            if (!empty($campaigns) ) {
                foreach ($campaigns as $value) {                   
                    $idCampaign[] = $value["ID"]; 
                              
                }
                foreach ($idCampaign as  $idData) {
                    $filters = ["campaign"=>$idData];
                    $idConts[$idData] = $this->mailjetClient->get(Resources::$Contactdata,['filters' =>$filters])->getData();
                    foreach ($idConts[$idData]  as  $idCont) {
                    $filters = ["id"=>$idCont["ContactID"]];
                    $allContacts[] = ["idCamp" => $idData,"contact"=>$this->mailjetClient->get(Resources::$Contact,['filters' =>$filters])->getData()];
                       
                    }
                }

                foreach ($allContacts as  $listCont) {
                  
                  $list[] = [$listCont["idCamp"], $listCont["contact"][0]["Email"]];
                }
                foreach ($list as  $res) {
                    $filters = ["id"=>$res[0]];
                    $prods[] = ["dest"=>$res[1],"result"=>$this->mailjetClient->get(Resources::$Campaign,['filters' =>$filters])->getData()[0]];                                      
                }
                foreach ($prods as  $prod) {
                $contacts[] = [
                    "sender" => $prod["result"]["FromEmail"],
                    "sujet" => $prod["result"]["Subject"],
                    "date" => $prod["result"]["CreatedAt"],
                    "emailTo" =>$prod["dest"]
                    ];
                }
                return $contacts;
            }
            return ""; 
        
        } elseif ($filtre =="last7days"){
            $date7 = new \DateTime();
            $date7->modify("-6 day");

            $campaigns7 = $this->getResultLastDay($date7);
            if (!empty($campaigns7)) {
                foreach ($campaigns7 as  $value) {
                    $idCampaign[] = $value["ID"];
                }
                foreach ($idCampaign as  $idData) {
                    $filters = ["campaign"=>$idData];
                    $idConts[$idData] = $this->mailjetClient->get(Resources::$Contactdata,['filters' =>$filters])->getData();
                    foreach ($idConts[$idData]  as  $idCont) {
                    $filters = ["id"=>$idCont["ContactID"]];
                    $allContacts[] = ["idCamp" => $idData,"contact"=>$this->mailjetClient->get(Resources::$Contact,['filters' =>$filters])->getData()];
                       
                    }
                }
                
                foreach ($allContacts as  $listCont) {
                  
                  $list[] = [$listCont["idCamp"], $listCont["contact"][0]["Email"]];
                }
                foreach ($list as  $res) {
                    $filters = ["id"=>$res[0]];
                    $prods[] = ["dest"=>$res[1],"result"=>$this->mailjetClient->get(Resources::$Campaign,['filters' =>$filters])->getData()[0]];                                      
                }
                foreach ($prods as  $prod) {
                $results[] = [
                    "sender" => $prod["result"]["FromEmail"],
                    "sujet" => $prod["result"]["Subject"],
                    "date" => $prod["result"]["CreatedAt"],
                    "emailTo" =>$prod["dest"]
                    ];
                }
                return $results;  
            }
           

        }
        return "";
    }

    protected function getResult($date)
    {
        $yest = $date->settime(0,0,0)->getTimestamp();
        $filters = ["fromts"=>(string)$yest];
        $format= $date->format("Y-m-d");
        $campaigns = $this->mailjetClient->get(Resources::$Campaign,['filters' =>$filters])->getData();
        dump($campaigns);
        if (!empty($campaigns)) {
            foreach ($campaigns as  $value) {
               $dateFiter = new \DateTime($value["CreatedAt"]);
               $time = $dateFiter->format("Y-m-d");
               if ($time === $format) {
                    $res[] = $value;                       
               }
            }
            return !empty($res)? $res: "";  
        }
       return "";
                    
    }

    protected function getResultLastDay($date)
    {
        $yest = $date->settime(0,0,0)->getTimestamp();
        $filters = ["fromts"=>(string)$yest];
        //$format= $date->format("Y-m-d");
        $campaigns = $this->mailjetClient->get(Resources::$Campaign,['filters' =>$filters])->getData();
        return $campaigns;
    }



}
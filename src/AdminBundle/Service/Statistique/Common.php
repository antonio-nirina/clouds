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

    public function getOneCampagne($id)
    {
        $filter = ["campaignid" => $id];
        $campaigns = $this->mailjetClient->get(Resources::$Campaign,['filters' => $filter])->getData()[0];
        $filters = ["contactslist" => $campaigns["ListID"]];
        $filter2 = ["limit" => 0];
        $listContactByCampaigns = $this->mailjetClient->get(Resources::$Campaignstatistics,['filters' => $filter2])->getData();
        foreach ($listContactByCampaigns as  $value) {
            if (intval($value["CampaignID"]) == intval($id)) {
                $status[] = $value;
            }         
        }
               
        $list = $this->mailjetClient->get(Resources::$Contactslist)->getData();
        foreach ($list as  $contact) {
            if ($contact["ID"] === $campaigns["ListID"]) {
               $vals[] = $contact;
            }
           
        }

        $emailToCampaign = $this->mailjetClient->get(Resources::$Contact,['filters' => $filters])->getData();
        foreach ($emailToCampaign as  $conts) {
            $emails[] = $conts["Email"];
        }

        $clickEvent = $this->mailjetClient->get(Resources::$Clickstatistics,['filters' => $filter])->getData();
        $openEvent =  $this->mailjetClient->get(Resources::$Openinformation,['filters' => $filter])->getData();
        if (!empty($clickEvent)) {
           foreach ($clickEvent as  $clicked) {
             $emailClicks[] = [
                "emails"=>$this->getContactById($clicked["ContactID"])["Email"],
                "etat"=>"click"
                ]; 
            
            }

            foreach ($emailClicks as  $clik) {
                $compClick[] = $clik["emails"];
        
            }
             foreach ($openEvent as  $opened) {
                $emailOpened[] = $this->getContactById($opened["ContactID"])["Email"]; 
            }
            $emailOpenClick = array_diff($emailOpened,$compClick);
            foreach ($emailOpenClick as  $value) {
                $resOpen[] = [
                "emails"=>$value,
                "etat" => "open"
                ]; 
            }
                   
        }

        if (!empty($openEvent) && empty($clickEvent)) {
            foreach ($openEvent as  $opened) {
                $emailOpened[] = [
                    "emails"=>$this->getContactById($opened["ContactID"])["Email"],
                    "etat"=>"open"
                    ]; 
            }
            foreach ($emailOpened as  $value) {
                $compOpenNotClick[] = $value["emails"];
        
            }
        }

        $bounced = $this->mailjetClient->get(Resources::$Bouncestatistics,['filters' => $filter])->getdata();
        if (!empty($bounced) && $bounced["IsBlocked"] == true) {
           foreach ($bounced[0] as  $bounce) {
             $emailBounced[] = [
                        "emails"=>$this->getContactById($bounce["ContactID"])["Email"],
                        "etat"=>"bloque"
                        ];         
            }

            foreach ($emailBounced as  $value) {
                $compBounced[] = $value["emails"];
        
            }            
        } 

        if (!empty($clickEvent) && !empty($openEvent) && !empty($bounced)) {           
            $newArray = array_diff($emails, $compClick,$emailOpenClick,$compBounced);
        } elseif (!empty($clickEvent) && !empty($openEvent) && empty($bounced)) {
            $check = array_diff($emails, $compClick,$emailOpenClick);
            $newArray =  $newArray = empty($check)?$compClick:$check;
        } elseif (empty($clickEvent) && !empty($openEvent) && empty($bounced)){
            $check = array_diff($emails, $compOpenNotClick);
            $newArray = empty($check)?$compOpenNotClick:$check;
        } elseif (empty($clickEvent) && empty($openEvent) && !empty($bounced)){
            $check = array_diff($emails, $compBounced);
            $newArray = empty($check)?$compBounced:$check;
        } else {
            $newArray = $emails;
        }
        foreach ($newArray as  $value) {
            $emailDel[] = ["emails"=>$value,"etat"=>"delivre"];
        }

        if (!empty($clickEvent) && !empty($openEvent) && !empty($bounced)) { 
            $result = array_merge($emailClicks,$resOpen,$emailBounced,$emailDel );
        }  elseif (!empty($clickEvent) && !empty($openEvent) && empty($bounced)) {
            $check = array_diff($emails, $compClick);
            if (!empty($resOpen) && !empty( $check )) {
              $result =  array_merge($emailClicks,$resOpen,$emailDel);
            } elseif (empty($resOpen) && !empty( $check )) {
                $result = array_merge($emailClicks,$emailDel);
            } elseif (empty($resOpen) && empty( $check )) {
                $result = $emailClicks;
            }
        
        } elseif (empty($clickEvent) && !empty($openEvent) && empty($bounced)){
            $check = array_diff($emails, $compOpenNotClick);
            $result = empty($check)?$emailOpened: array_merge($emailOpened,$emailDel);
           
        } elseif (empty($clickEvent) && empty($openEvent) && !empty($bounced)){
            $check = array_diff($emails, $compBounced);
            $result = empty($check)?$emailBounced:array_merge($emailBounced,$emailDel);
        } else {
            $result = $emailDel;
        }

        $json = new JsonResponse($result);
        $data = $json->getContent(); 
        $name = empty($vals[0]["Name"])?"":$vals[0]["Name"];
        $template = $this->getTempalte($id);
        $results = [
            "listContact"=>$name,
            "status"=>$status[0],
            "email"=>$result,
            "template" =>$template,
            "data" =>$data];
        return $results;
    }

    public function getListCampaign($id)
    {
        $filter = ["campaignid" => $id];
        $campaigns = $this->mailjetClient->get(Resources::$Campaign,['filters' => $filter])->getData()[0];

        $template = $this->getTempalte($campaigns["ListID"]);
        $results = ["campaigns" =>$campaigns,"template"=> $template];
        return $results;
    }

    public function getTempalte($id)
    {
        $filter = ["campaignid" => $id];
        $campaignDraft = $this->mailjetClient->get(Resources::$Campaigndraft,['filters' => $filter])->getData()[0];
        $template = $this->mailjetClient->get(Resources::$Template,['id' => $campaignDraft["TemplateID"]])->getData()[0];
        return $template["Name"];
    }

    public function getContactById($idContact)
    {
        $response = $this->mailjetClient->get(Resources::$Contact, ['id' => $idContact]);
        return $response->getData()[0];
    }



}
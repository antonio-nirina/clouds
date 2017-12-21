<?php
namespace BeneficiaryBundle\Controller;

use AdminBundle\Component\Post\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AdminBundle\Entity\HomePageSlide;

class PageController extends Controller
{
    /**
     * @Route("/", name="beneficiary_home")
     */
    public function homePageAction()
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $home_page_data = $program->getHomePageData();
        if (is_null($home_page_data)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $editorial = $home_page_data->getEditorial();
        if (is_null($editorial)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $table_network = $program->getSiteTableNetworkSetting();
        $has_network = false;
        if ($table_network->getHasFacebook() || $table_network->getHasLinkedin() || $table_network->getHasTwitter()) {
            $has_network = true;
        }

        $background_link = '';
        if ($background = $program->getSiteDesignSetting()->getBodyBackground()) {
            $background_link = $this->container->getParameter('background_path').'/'.$program->getId().'/'.$background;
        }
                
        $em = $this->getDoctrine()->getManager();
        $ordered_slide_list = $em->getRepository('AdminBundle\Entity\HomePageSlide')
            ->findByHomePageDataOrdered($home_page_data);

        $home_page_post_list = $em->getRepository('AdminBundle\Entity\HomePagePost')
            ->findBy(array('program' => $program), array('created_at' => 'DESC'));

        return $this->render('BeneficiaryBundle:Page:home.html.twig', array(
            'editorial' => $editorial,
            'has_network' => $has_network,
            'table_network' => $table_network,
            'slide_list' => $ordered_slide_list,
            'background_link' => $background_link,
            'home_page_post_list' => $home_page_post_list,
            'home_page_post_type_class' => new PostType(),
        ));
    }
	
	/**
     * @Route("/beneficiary-home/video/lecture", name="beneficiary_home_video_lecture")
     */
    public function LectureVideoAction(Request $request){
		$program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }
		$em = $this->getDoctrine()->getManager();
		
		if ($request->isMethod('POST')) {
			$IdVideo = $request->get('video_id');
			$VideoSlide = $em->getRepository("AdminBundle:HomePageSlide")->find($IdVideo);
			$response = $this->forward('BeneficiaryBundle:PartialPage:afficheLecteurVideo',array('videos' => $VideoSlide, 'programm' => $program));
			return new Response($response->getContent());
		}else{
			return new Response('');
		}
	}
	
	
	public function PageStandardFooterAction() {
		$program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }
		
		$em = $this->getDoctrine()->getManager();
		$PageStandard = $em->getRepository('AdminBundle:SitePagesStandardSetting')->findByProgram($program);
		
		$ListePages = array();
		foreach($PageStandard as $Pages){
			if($Pages->getStatusPage() == '1'){
				if($Pages->getNomPage() == 'mentions légales' || $Pages->getNomPage() == 'règlement' || $Pages->getNomPage() == 'contact'){
					$ListePages[] = $Pages;
				}
			}
		}
		
		
		return $this->render('BeneficiaryBundle::page_footer.html.twig', array(
			'ListePages' => $ListePages
		));
    }
	
	public function PageStandardTopAction() {
		$program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }
		
		$em = $this->getDoctrine()->getManager();
		$PageStandard = $em->getRepository('AdminBundle:SitePagesStandardSetting')->findByProgram($program);
		
		$ListePages = array();
		foreach($PageStandard as $Pages){
			if($Pages->getStatusPage() == '1'){
				if($Pages->getNomPage() != 'mentions légales' && $Pages->getNomPage() != 'règlement' && $Pages->getNomPage() != 'contact'){
					$ListePages[] = $Pages;
				}
			}
		}
		
		
		return $this->render('BeneficiaryBundle::menu_top_niv_2.html.twig', array(
			'ListePages' => $ListePages
		));
    }
	
	public function PageStandardContactAction(){
		$program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }
		
		$em = $this->getDoctrine()->getManager();
		$PageStandard = $em->getRepository('AdminBundle:SitePagesStandardSetting')->findByProgram($program);
		
		$ListePages = array();
		$est_page_contact = false;
		foreach($PageStandard as $Pages){
			if($Pages->getStatusPage() == '1' && $Pages->getNomPage() == 'contact'){
				$ListePages = $Pages;
				$est_page_contact = true;
			}
		}
		
		
		return $this->render('BeneficiaryBundle::block-contact.html.twig', array(
			'ListePages' => $ListePages,
			'est_page_contact' => $est_page_contact
		));
	}
	

	/**
     * @Route(
     *     "/beneficiary-home/pages/{id}",
     *     name="beneficiary_home_pages_standard"),
     *     requirements={"id": "\d+"}
     */
    public function AffichePagesStandardAction($id){
		$program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }
		
		$table_network = $program->getSiteTableNetworkSetting();
        $has_network = false;
        if ($table_network->getHasFacebook() || $table_network->getHasLinkedin() || $table_network->getHasTwitter()) {
            $has_network = true;
        }
		
		$background_link = '';
        if ($background = $program->getSiteDesignSetting()->getBodyBackground()) {
            $background_link = $this->container->getParameter('background_path').'/'.$program->getId().'/'.$background;
        }
		
		$em = $this->getDoctrine()->getManager();
		$Pages = $em->getRepository('AdminBundle:SitePagesStandardSetting')->find($id);
		
		if(is_null($Pages)){
			return $this->redirectToRoute('beneficiary_home');
		}
		
		return $this->render('BeneficiaryBundle:Page:AffichePagesStandard.html.twig', array(
            'has_network' => $has_network,
            'table_network' => $table_network,
            'background_link' => $background_link,
			'Pages' => $Pages
        ));
	}
	
	/**
     * @Route(
     *     "/pages/{slug}",
     *     name="beneficiary_home_pages_standard_slug")
     */
    public function AffichePagesStandardSlugAction($slug){
		$program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }
		
		$table_network = $program->getSiteTableNetworkSetting();
        $has_network = false;
        if ($table_network->getHasFacebook() || $table_network->getHasLinkedin() || $table_network->getHasTwitter()) {
            $has_network = true;
        }
		
		$background_link = '';
        if ($background = $program->getSiteDesignSetting()->getBodyBackground()) {
            $background_link = $this->container->getParameter('background_path').'/'.$program->getId().'/'.$background;
        }
		
		$em = $this->getDoctrine()->getManager();
		$Pages = $em->getRepository('AdminBundle:SitePagesStandardSetting')->findOneBy(array(
           'slug' => $slug
        ));
		
		if(is_null($Pages)){
			return $this->redirectToRoute('beneficiary_home');
		}
		
		if($Pages->getStatusPage() != '1'){
			return $this->redirectToRoute('beneficiary_home');
		}
		
		$Options = array();
		$Options = $Pages->getOptions();
		
		// Obtient une liste de colonnes
		foreach ($Options as $key => $row) {
			$ordre[$key]  = $row['ordre'];
		}
		array_multisort($ordre, SORT_ASC, $Options);
		
		
		return $this->render('BeneficiaryBundle:Page:AffichePagesStandard.html.twig', array(
            'has_network' => $has_network,
            'table_network' => $table_network,
            'background_link' => $background_link,
			'Pages' => $Pages,
			'Options' => $Options
        ));
	}
}
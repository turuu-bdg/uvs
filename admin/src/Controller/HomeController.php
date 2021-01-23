<?php


namespace App\Controller;


use App\Entity\Content;
use App\Entity\Maingroup;
use App\Form\MainType;
use App\Form\Type\TestFrom;
use Doctrine\DBAL\Types\DateType;
use Doctrine\DBAL\Types\TextType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     *
      * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER') or is_granted('ROLE_SUPER_ADMIN')")
     */
    public function index(){

        $uId= $this->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $dql = "SELECT m FROM App\Entity\Module m INNER JOIN App\Entity\UserPermission p WITH p.moduleId=m.id WHERE p.userId =:id and p.status=1";
        $query = $em->createQuery($dql);
        $query->setParameter('id', $uId);
        $per = $query->getResult();
        $dql = "SELECT m FROM App\Entity\Module m  WHERE m.subMenuId !=0";
        $query = $em->createQuery($dql);
        $sub = $query->getResult();
        return $this->render('menu.html.twig',[
            'per'=>$per,
            'sub'=>$sub
        ]);
    }


    /**
     * @Route("/index", name="index")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER') or is_granted('ROLE_SUPER_ADMIN')")
     */
    public function dashboard()
    {
        return $this->render('index.html.twig');

    }


}
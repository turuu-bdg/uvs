<?php


namespace App\Controller;


use App\Entity\Maingroup;
use App\Entity\User;
use App\Entity\UserPermission;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsersController extends AbstractController
{
    /**
     * @Route("/users")
      * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER') or is_granted('ROLE_SUPER_ADMIN')")
     */
    public function index(){
        $em = $this->getDoctrine()->getManager();
        $dql = "SELECT u FROM App\Entity\User u where u.roles!=1";
        $query = $em->createQuery($dql);
        $main = $query->getResult();
        return $this->render('users/index.html.twig',[
            'user'=>$main,
        ]);
    }

    /**
     * @Route("/changePassword" , name="changePassword")
      * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER') or is_granted('ROLE_SUPER_ADMIN')")
     */
    public function changepass(){
        return $this->render('users/changepass.html.twig');
    }

    /**
     * @Route("/createUser", name="createUser")
      * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN')")
     */
    public function create(){
        $em = $this->getDoctrine()->getManager();
        $dql = "SELECT r FROM App\Entity\Roles r where r.id != 1";
        $query = $em->createQuery($dql);
        $roll = $query->getResult();

        $dql = "SELECT m FROM App\Entity\Module m";
        $query = $em->createQuery($dql);
        $module = $query->getResult();

        return $this->render('users/create.html.twig',[
            'roll'=>$roll,
            'module'=>$module
        ]);
    }

    /**
     * @Route("/insertUser", name="insertUser")
      * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN')")
     */
    public function insertDB(UserPasswordEncoderInterface $encoder): Response
    {
        $em = $this->getDoctrine()->getManager();

        $name=$_POST["name"];
        $pass = $_POST["password"];
        $roles = $_POST["roll"];

        $user = new User();
        $encodePass = $encoder->encodePassword($user, $pass);
        $user->setUsername("$name");
        $user->setPassword("$encodePass");
        $user->setRoles("$roles");

        $em->persist($user);
        $em->flush();

        $dql = "SELECT u FROM App\Entity\User u WHERE u.username= :name";
        $query = $em->CreateQuery($dql);
        $query->setParameter('name', $name);
        $content = $query->getResult();

        foreach ($content as $row) {
            $u_id = $row->getId();
            $dql = "SELECT m FROM App\Entity\Module m";
            $query = $em->createQuery($dql);
            $module = $query->getResult();
            foreach ($_POST['module'] as $check) {
                $con = new UserPermission();
//
                $con->setUserId("$u_id");
                $con->setModuleId($check);
                $con->setStatus(1);

                $em->persist($con);
                $em->flush();
            }
        }
        $this->addFlash('success', 'Таны оруулсан мэдээний төрөл амжилттай хадгалагдлаа');
        return $this->redirectToRoute('users');


    }


    /**
     * @Route("/EditUser/{slug}", name="EditUser")
      * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN')")
     */
    public function edit($slug){
        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT u FROM App\Entity\User u WHERE u.id=:id";
        $query = $em->createQuery($dql);
        $query->setParameter('id', $slug);
        $user = $query->getResult();

        $dql = "SELECT u.id as uId, u.username,p.moduleId FROM App\Entity\User u LEFT JOIN App\Entity\UserPermission p WITH u.id=p.userId WHERE p.status=1 and u.id=:id";
        $query = $em->createQuery($dql);
        $query->setParameter('id', $slug);
        $per = $query->getResult();

        $dql = "SELECT r FROM App\Entity\Roles r where r.id != 1";
        $query = $em->createQuery($dql);
        $roll = $query->getResult();

        $dql = "SELECT m FROM App\Entity\Module m ";
        $query = $em->createQuery($dql);
        $module = $query->getResult();

        return $this->render('users/edit.html.twig',[
            'roll'=>$roll,
            'module'=>$module,
            'user'=>$user,
            'per'=>$per
        ]);
    }
    /**
     * @Route("/UpdateUser", name="UpdateUser")
      * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER') or is_granted('ROLE_SUPER_ADMIN')")
     */
    public function update(){
        $uId = $_POST['uId'];
        $em = $this->getDoctrine()->getManager();
        $dql = "SELECT m FROM App\Entity\Module m";
        $query = $em->createQuery($dql);
        $module = $query->getResult();
        $qb = $em->createQueryBuilder();
        $q = $qb->update('App\Entity\UserPermission', 'p')
            ->set('p.status' , 0)
            ->where('p.userId = ?1')
            ->setParameter(1, $uId)
            ->getQuery();
        $p = $q->execute();
        foreach ($module as $row){

            foreach($_POST['module'] as $check) {
                $dql= "SELECT p FROM App\Entity\UserPermission p WHERE p.userId=:uId and p.moduleId=:mId";
                $query = $em->createQuery($dql);
                $query->setParameters([
                    'uId'=>$uId,
                    'mId'=>$check
                ]);
                $permission = $query->getResult();
                if(!empty($permission)){
                    $qb = $em->createQueryBuilder();
                    $q = $qb->update('App\Entity\UserPermission', 'p')
                        ->set('p.status' , 1)
                        ->where('p.userId = ?1')
                        ->andWhere('p.moduleId = ?2')
                        ->setParameter(1, $uId)
                        ->setParameter(2, $check)
                        ->getQuery();
                    $p = $q->execute();

//                    $dql= "SELECT p FROM App\Entity\UserPermission p WHERE p.userId=:uId";
//                    $query = $em->createQuery($dql);
//                    $query->setParameter('uId',$uId);
//                    $uPer = $query->getResult();
//                    foreach ($uPer as $rows){
//                        $moduleId= $rows->getModuleId();
//                        if($moduleId != $check){
//                            $qb = $em->createQueryBuilder();
//                            $q = $qb->update('App\Entity\UserPermission', 'p')
//                                ->set('p.status' , 0)
//                                ->where('p.userId = ?1')
//                                ->andWhere('p.moduleId != ?2')
//                                ->setParameter(1, $uId)
//                                ->setParameter(2, $moduleId)
//                                ->getQuery();
//                            $p = $q->execute();
//                        }
//                    }
//
                }
                else{
                    $con = new UserPermission();
//
                    $con->setUserId("$uId");
                    $con->setModuleId($check);
                    $con->setStatus(1);

                    $em->persist($con);
                    $em->flush();
                }
            }

        }
        $this->addFlash('success', 'Таны оруулсан хэрэглэгчийн мэдээлэл амжилттай шинэчлэгдлээ');
        return $this->redirectToRoute('users');

    }

    /**
     * @Route("/deleteUser", name="deleteUser")
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     */
    public function deleteUser(): Response
    {
        $em = $this->getDoctrine()->getManager();

        $id =$_POST["id"];
        $content = $em->getRepository(User::class)->find($id);
        if (!$content) {
            throw $this->createNotFoundException(
                'Ийм контент байхгүй байна'.$id
            );
        }
        $em->remove($content);
        $em->flush();

        $qb = $em->createQueryBuilder();
        $qb->delete()
            ->from('App\Entity\UserPermission', 'p')
            ->where('p.userId = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();

//        $queryBuilder = $em->createQueryBuilder();
//        $queryBuilder
//            ->delete('App\Entity\UserPermission', 'u')
//            ->where($queryBuilder->expr()->eq('u.id', ':userId'));
//        $gh = $em->getRepository(UserPermission::class)->findOneBy(['userId' => $id]);
//        if ($gh) {
//            $em->remove($gh);
//            $em->flush();
//        }else{
//            throw $this->createNotFoundException(
//                'Ийм контент байхгүй байна'.$id
//            );
//        }



        $this->addFlash('success', "Хэрэглэгчийн мэдээлэл амжилттай устгагдлаа");
        return $this->redirectToRoute('users');

    }
}
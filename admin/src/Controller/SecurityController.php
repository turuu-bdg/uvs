<?php

namespace App\Controller;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    /**
     * @Route("/changePass", name="changePass")
      * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER') or is_granted('ROLE_SUPER_ADMIN')")
     */
    public function change_user_password(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $old_pwd = $request->get('old_password');
        $new_pwd = $request->get('new_password');
        $new_pwd_confirm = $request->get('new_password_confirm');
        $user = $this->getUser();
        $checkPass = $passwordEncoder->isPasswordValid($user, $old_pwd);
        $em = $this->getDoctrine()->getManager();
        if ($checkPass === true) {
            $uId = $this->getUser()->getId();
            $content = $em->getRepository(User::class)->find($uId);
//            if (!$content) {
//                throw $this->createNotFoundException(
//                    'Ийм контент байхгүй байна'.$uId
//                );
//            }

            $encodePass = $passwordEncoder->encodePassword($user, $new_pwd);
            $content->setPassword($encodePass);
////        $gh = new Groupheader();
//            $data =$_POST["message1"];

//            $content->setContent("$new_pwd");
//            $content->setCreateDate(new \DateTime('now', new \DateTimeZone('Asia/Ulaanbaatar')));
            $em->flush();
        } else {
            $this->addFlash('error', 'Таны өмнөх нууц үг тохирохгүй байна. Шалгаад дахин оролдоно уу?');
            return $this->redirectToRoute('changePassword');
        }
        $this->addFlash('success', 'Таны нууц үг амжилттай хадгалагдлаа');
        return $this->redirectToRoute('index');
    }

}

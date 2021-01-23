<?php


namespace App\Lib;


use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Builder\Class_;

class GetMsgContent
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        // you *must* call the parent constructor
//        parent::__construct();
    }
    public function index($id)
    {

        $today = date("Y-m-d");
        $day = date('Y-m-d', strtotime("tomorrow"));
//        $em = $this->getDoctrine()->getManager();
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('con')
            ->from('App\Entity\Content', 'con')
            ->where('con.group = :id')
            ->setParameter('id', $id);
        $cont = $qb->getQuery()->getResult();
//        var_dump($cont);
//        exit;
        if($cont){
//            var_dump($id);
//            exit;
            if($id>=65 && $id <=92){
                $qb = $this->entityManager->createQueryBuilder();
                $qb->select('c')
                    ->from('App\Entity\Content', 'c')
                    ->where('c.group = :id')
                    ->andWhere('c.date = :day')
                    ->setParameter('id', $id)
                    ->setParameter('day', $day);
                $content= $qb->getQuery()->getResult();
            }
            else{
                $qb = $this->entityManager->createQueryBuilder();
                $qb->select('c')
                    ->from('App\Entity\Content', 'c')
                    ->where('c.group = :id')
                    ->andWhere('c.date = :day')
                    ->setParameter('id', $id)
                    ->setParameter('day', $today);
                $content= $qb->getQuery()->getResult();
            }


            if($content){
                foreach ($content as $c) {
                    $text = $c->getContent();
                }
//                $text = $content->getContent();
                if($text==' '){
                    $war = $this->entityManager->createQueryBuilder();
                    $war->select('wt')
                        ->from('App\Entity\WarningText', 'wt')
                        ->where('wt.warId = 25');
                    $warText= $war->getQuery()->getResult();
                    foreach ($warText as $c) {
                        $war = $c->getWarText();
                    }
                    if($id>=65 && $id <=92) {
                        $text = $day . " " . $war;
                    }else {
                        $text = $today . " " . $war;
                    }
                }
            }
            else{
                $war = $this->entityManager->createQueryBuilder();
                $war->select('wt')
                    ->from('App\Entity\WarningText', 'wt')
                    ->where('wt.warId = 25');
                $warText= $war->getQuery()->getResult();
                foreach ($warText as $c) {
                    $war = $c->getWarText();
                }
                if($id>=65 && $id <=92) {
                    $text = $day . " " . $war;
                }else {
                    $text = $today . " " . $war;
                }
            }
        }
        else{
            $war = $this->entityManager->createQueryBuilder();
            $war->select('wt')
                ->from('App\Entity\WarningText', 'wt')
                ->where('wt.warId = 1');
            $warText = $war->getQuery()->getResult();
            foreach ($warText as $c) {
                $war = $c->getWarText();
            }
            $text = $war;
        }
//        var_dump($text);
//        exit;
        return $text;
    }

    public function userCheck($userId){
        $functionName = __FUNCTION__;
        $File = "../var/log/".$functionName."Log_".date("Y-m").".log";
        $Handle = fopen($File, 'a');
        $nowTime= (new \DateTime('now', new \DateTimeZone('Asia/Ulaanbaatar')));
        fwrite($Handle, "userId: $userId \n");
//        fwrite($Handle, "deviceId: $userId \n");
//        $userSession = $this->entityManager->getRepository(UserSession::class)->findOneBy(['userId'=>$userId]);
//        if(empty($userSession)){
//            $userSession = new UserSession();
//            $userSession->setUserId($userId);
//            $userSession->setCreatedAt($nowTime);
//            $userSession->updateSession();
//            $this->entityManager->persist($userSession);
//        }else{
//            $userSession->updateSession();
//        }
//        $this->entityManager->flush();

//        $token = bin2hex(openssl_random_pseudo_bytes(4));
//        echo $userId;
        $reqData = [
            'status'=>"success",
            'user_id'=>$userId
        ];
        return $reqData;
    }
}
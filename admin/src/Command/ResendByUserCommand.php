<?php

namespace App\Command;

use App\Controller\SendMessageController;
use App\Entity\SendMessageLog;
use App\Lib\GetMsgContent;
use App\Lib\ProcessUrl;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ResendByUserCommand extends Command
{
    protected static $defaultName = 'ResendByUser';
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        // you *must* call the parent constructor
        parent::__construct();
    }
    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $timeZone = (new \DateTime('now', new \DateTimeZone('Asia/Ulaanbaatar')));
        $time = date_format($timeZone, "H");
//        $File = "var/log/log_".date("Y-m").".log";
//        $Handle = fopen($File, 'a');

        $today = date("Y-m-d");
        $qb = $this->entityManager->createQueryBuilder();
        if($time == '07'){
//            fwrite($Handle, "\n\n********************** Дорнын зурхай ***********************\n\n");
            $qb->select('m')
                ->from('App\Entity\MsgPackage', 'm')
                ->where('m.status = 1')
                ->andWhere('m.dateUntil >= :date')
                ->andWhere('m.maingroupId = 1')
                ->andWhere('m.groupId = 93')
                ->setParameter('date', $today);
        }elseif ($time == '08'){
//            fwrite($Handle, "\n\n********************** Өрнийн зурхай ***********************\n\n");
            $qb->select('m')
                ->from('App\Entity\MsgPackage', 'm')
                ->where('m.status = 1')
                ->andWhere('m.dateUntil >= :date')
                ->andWhere('m.maingroupId = 1')
                ->andWhere('m.groupId >= 94')
                ->andWhere('m.groupId <= 105')
                ->setParameter('date', $today);
        }elseif ($time == '09'){
            $qb->select('m')
                ->from('App\Entity\MsgPackage', 'm')
                ->where('m.status = 1')
                ->andWhere('m.dateUntil >= :date')
                ->andWhere('m.maingroupId = 10')
                ->setParameter('date', $today);
        }elseif ($time == '13') {
//            fwrite($Handle, "\n\n********************** Ханшийн мэдээ ***********************\n\n");
            $qb->select('m')
                ->from('App\Entity\MsgPackage', 'm')
                ->where('m.status = 1')
                ->andWhere('m.dateUntil >= :date')
                ->andWhere('m.maingroupId = 3')
                ->andWhere('m.number = 98101586')
                ->setParameter('date', $today);
        }elseif ($time == '21') {
//            fwrite($Handle, "\n\n********************** Цаг агаар ***********************\n\n");
            $qb->select('m')
                ->from('App\Entity\MsgPackage', 'm')
                ->where('m.status = 1')
                ->andWhere('m.dateUntil >= :date')
                ->andWhere('m.maingroupId = 2')
                ->setParameter('date', $today);

        }

        $packages = $qb->getQuery()->getResult();

        foreach ($packages as $package){
//            $number = $package->getNumber();
             $number = "98101586";
            $gId = $package->getGroupId();
            $mId = $package->getMaingroupId();

            $cont = new GetMsgContent($this->entityManager);
            $text = $cont->index($gId);

            echo $number . ": " . $text . "\n";
            $log = new SendMessageLog();
            $log->setContent($text);
            $log->setNumber($number);
            $log->setMaingroup($mId);
            $log->setGroupname($gId);
            $log->setDatetime(new \DateTime('now', new \DateTimeZone('Asia/Ulaanbaatar')));
            $this->entityManager->persist($log);
            $this->entityManager->flush();

            $len = strlen($text);
            $j=$len/160;
            for($i=0; $i<$j; $i++){
                $myurl = "http://192.88.80.199:13013/cgi-bin/sendsms?username=javxa&password=javxa123&to=".$number."&text=".urlencode(substr($text,$i*160,160));
                $pro = new ProcessUrl();
                $res = $pro->processURL($myurl);
                $Data = date("Y-m-d H:i:s")." - ".$number." - ".urlencode(substr($text,$i*160,160))."\n";
//                fwrite($Handle, $Data);
            }
        }
//        fclose($Handle);
        return Command::SUCCESS;
    }
}

<?php 

namespace App\Service;

use Twig\Environment;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use App\Repository\TransactionalRepository;


class MailService
{
    
    protected $mailer;
    protected $mailFrom;
    protected $twig;
    protected $transactionalRepository;

    public function __construct(
        MailerInterface $mailer, 
        Environment $twig,
        TransactionalRepository $transactionalRepository,
        $mailFrom
        )
    {
        $this->mailer = $mailer;
        $this->mailFrom = $mailFrom;
        $this->twig = $twig;
        $this->transactionalRepository = $transactionalRepository;
    }

    /*
    * $datas Array
    * to : recipient email : required
    * tpl : email transactionnal template
    * vars : Array of objects and vars for message variables (['mavar1' => 'var1 content', ..., 'user' => User object, ...])
    * usage eg. : $mailService->sendMail([
    *               'to' => 'email@domain.tld',
    *               'tpl' => 'template_name',
    *               'entities' => ['user' => User object, ...]
    *              ]);
    */
    public function sendMail($datas)
    {
        $template = $this->transactionalRepository->findOneBy(['template' => $datas['tpl']]);
        if($template === NULL) {
            return false;
        }

        $htmlMsg = $this->twig->render('emails/' . $datas['tpl'] . '.html.twig', $datas['vars']);
        dd($htmlMsg);

        $email = new Email();
        $email->from($this->mailFrom)
            ->to($datas['to'])
            ->replyTo($this->mailFrom)
            ->priority(Email::PRIORITY_HIGH)
            ->subject($template->getSubject())
            ->html($htmlMsg);

        $this->mailer->send($email);
        
        return true;
    }
    
}
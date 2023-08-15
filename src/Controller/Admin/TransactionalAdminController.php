<?php

namespace App\Controller\Admin;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TransactionalRepository;
use App\Form\TransactionalType;
use App\Entity\Transactional;
use App\Repository\UserRepository;
use App\Service\MailService;

#[Route('/admin/transactional')]
class TransactionalAdminController extends AbstractController
{
    private $em;
    protected $transacFolder;

    public function __construct(EntityManagerInterface $em, $transacFolder)
    {
        $this->em = $em;
        $this->transacFolder = $transacFolder;
    }
    
    #[Route('/', name: 'app_transactional_admin_index', methods: ['GET'])]
    public function index(TransactionalRepository $transactionalRepository, MailService $mailService, UserRepository $userRepository): Response
    {
    
        return $this->render('admin/transactional_admin/index.html.twig', [
            'transactionals' => $transactionalRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_transactional_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $transactional = new Transactional();
        $form = $this->createForm(TransactionalType::class, $transactional);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($transactional);
            $this->em->flush();

            return $this->redirectToRoute('app_transactional_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/transactional_admin/new.html.twig', [
            'transactional' => $transactional,
            'form' => $form,
            'variables' => $this->listTables()
        ]);
    }

    #[Route('/{id}', name: 'app_transactional_admin_show', methods: ['GET'])]
    public function show(Transactional $transactional): Response
    {
        $template = $this->renderView('emails/base_email.html.twig', [
            'content' => $transactional->getContent(),
        ]);
        return $this->render('admin/transactional_admin/show.html.twig', [
            'transactional' => $transactional,
            'template' => $template
        ]);
    }

    #[Route('/{id}/edit', name: 'app_transactional_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Transactional $transactional): Response
    {
        $form = $this->createForm(TransactionalType::class, $transactional);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $file = fopen($this->transacFolder . '/' . $transactional->getTemplate() . '.html.twig', 'w+');
            $text = '{% extends "emails/base_email.html.twig" %}{% block content %}' . $transactional->getContent() . '{% endblock %}';
            fwrite($file, $text);
            fclose($file);

            return $this->redirectToRoute('app_transactional_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/transactional_admin/edit.html.twig', [
            'transactional' => $transactional,
            'form' => $form,
            'variables' => $this->listTables()
        ]);
    }

    #[Route('/{id}', name: 'app_transactional_admin_delete', methods: ['POST'])]
    public function delete(Request $request, Transactional $transactional): Response
    {
        if ($this->isCsrfTokenValid('delete'.$transactional->getId(), $request->request->get('_token'))) {
            $this->em->remove($transactional);
            $this->em->flush();
        }

        return $this->redirectToRoute('app_transactional_admin_index', [], Response::HTTP_SEE_OTHER);
    }

    private function listTables() {
        $allMetadata = $this->em->getMetadataFactory()->getAllMetadata();
        $schemaManager = $this->em->getConnection()->getSchemaManager();
        $tableNames = array_map(
            function(ClassMetadata $meta) {
                return $meta->getTableName();
            },
            $allMetadata);
        $allowedes = [
            ['table' => 'adverts', 'name' => 'Annonces'],
            ['table' => 'company', 'name' => 'Associations / éleveurs'], 
            ['table' => 'petitions', 'name' => 'Pétitions'], 
            ['table' => 'team', 'name' => 'Administrateurs'], 
            ['table' => 'user', 'name' => 'Utilisateurs']
        ];
        $i = 0;
        foreach($tableNames as $table) {
            foreach($allowedes as $allowed) {
                if($table == $allowed['table']) {
                    $tableNames[$i] = ['table' => $table, 'name' => $allowed['name']];
                    $columns = $schemaManager->listTableColumns($table);
        
                    $c = 0;
                    $columnNames = [];
                    $exclude = ['id', 'status', 'roles', 'is_pro'];
                    foreach($columns as $column){
                        if(in_array($column->getName(), $exclude) || preg_match('#^(fk_)#', $column->getName())) {
                            unset($columns[$c]);
                            continue;
                        }
                        $tab = [
                            'columnName' => $column->getName(),
                            'comment' =>$column->getComment()
                        ];
                        array_push($columnNames, $tab);
                        $c++;
                    }
                    $tableNames[$i]['columns'] = $columnNames;
                } else {
                    unset($tableNames[$i]);
                }
                $i++;
            }
        }
        
        return $tableNames;
    }
}

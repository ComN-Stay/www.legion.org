<?php

namespace App\Controller\Admin;

use App\Entity\Transactional;
use App\Form\TransactionalType;
use App\Entity\TransactionalVars;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TransactionalRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\TransactionalVarsRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/admin/transactional')]
class TransactionalAdminController extends AbstractController
{
    private $em;
    protected $transacFolder;

    public function __construct(
        EntityManagerInterface $em, 
        $transacFolder
    )
    {
        $this->em = $em;
        $this->transacFolder = $transacFolder;
    }
    
    #[Route('/', name: 'app_transactional_admin_index', methods: ['GET'])]
    public function index(
        TransactionalRepository $transactionalRepository, 
        TransactionalVarsRepository $transactionalVarsRepository,
    ): Response
    {
        $vars = $transactionalVarsRepository->findAll();
        $tab = [];
        foreach($vars as $val) {
            $tab[$val->getVarTable() . '|' . $val->getVarField()] = $val->getDescription();
        }
        return $this->render('admin/transactional_admin/index.html.twig', [
            'transactionals' => $transactionalRepository->findAll(),
            'sidebar' => 'params',
            'variables' => $tab,
            'tables' => $this->listTables(),
        ]);
    }

    #[Route('/new', name: 'app_transactional_admin_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        TransactionalVarsRepository $transactionalVarsRepository,
    ): Response
    {
        $transactional = new Transactional();
        $form = $this->createForm(TransactionalType::class, $transactional);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($transactional);
            $this->em->flush();
            $file = fopen($this->transacFolder . '/' . $transactional->getTemplate() . '.html.twig', 'w+');
            $sanitizedText = str_replace('%20', ' ', $transactional->getContent());
            $text = '{% extends "emails/base_email.html.twig" %}{% block content %}' . $sanitizedText . '{% endblock %}';
            fwrite($file, $text);
            fclose($file);

            return $this->redirectToRoute('app_transactional_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        $variables = $this->listVars($transactionalVarsRepository);
        
        return $this->render('admin/transactional_admin/new.html.twig', [
            'transactional' => $transactional,
            'form' => $form,
            'variables' => $variables,
            'sidebar' => 'params'
        ]);
    }

    #[Route('/manageVars', name: 'app_transactional_admin_manage_vars', methods: ['POST'])]
    public function manageVars(
        TransactionalVarsRepository $transactionalVarsRepository,
        Request $request
    ): JsonResponse
    {
        $vars = json_decode($request->get('tab'));
        $initials = $transactionalVarsRepository->findAll();
        foreach($initials as $initial) {
            $this->em->remove($initial);
            $this->em->flush();
        }
        foreach($vars as $var) {
            $exp = explode('|', $var[0]);
            $transacVar = $transactionalVarsRepository->findOneBy(['var_table' => $exp[0], 'var_field' => $exp[1]]);
            if($transacVar === null) {
                $transacVar = new TransactionalVars;
                $transacVar->setVarTable($exp[0]);
                $transacVar->setVarField($exp[1]);
            }
            $transacVar->setDescription($var[1]);
            $this->em->persist($transacVar);
        }

        $this->em->flush();
        return new JsonResponse(['result' =>'success']);
    }

    #[Route('/{id}', name: 'app_transactional_admin_show', methods: ['GET'])]
    public function show(
        Transactional $transactional
    ): Response
    {
        $template = $this->renderView('emails/base_email.html.twig', [
            'content' => $transactional->getContent(),
        ]);
        return $this->render('admin/transactional_admin/show.html.twig', [
            'transactional' => $transactional,
            'template' => $template,
            'sidebar' => 'params'
        ]);
    }

    #[Route('/{id}/edit', name: 'app_transactional_admin_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        Transactional $transactional,
        TransactionalVarsRepository $transactionalVarsRepository,
    ): Response
    {
        $form = $this->createForm(TransactionalType::class, $transactional);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $file = fopen($this->transacFolder . '/' . $transactional->getTemplate() . '.html.twig', 'w+');
            $sanitizedText = str_replace('%20', ' ', $transactional->getContent());
            $text = '{% extends "emails/base_email.html.twig" %}{% block content %}' . $sanitizedText . '{% endblock %}';
            fwrite($file, $text);
            fclose($file);

            return $this->redirectToRoute('app_transactional_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        $variables = $this->listVars($transactionalVarsRepository);
        
        return $this->render('admin/transactional_admin/edit.html.twig', [
            'transactional' => $transactional,
            'form' => $form,
            'variables' => $variables,
            'sidebar' => 'params'
        ]);
    }

    #[Route('/{id}', name: 'app_transactional_admin_delete', methods: ['POST'])]
    public function delete(
        Request $request, 
        Transactional $transactional
    ): Response
    {
        if ($this->isCsrfTokenValid('delete'.$transactional->getId(), $request->request->get('_token'))) {
            $this->em->remove($transactional);
            $this->em->flush();
        }

        return $this->redirectToRoute('app_transactional_admin_index', [], Response::HTTP_SEE_OTHER);
    }

    private function listTables() {
        $allMetadata = $this->em->getMetadataFactory()->getAllMetadata();
        $schemaManager = $this->em->getConnection()->createSchemaManager();
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
            ['table' => 'user', 'name' => 'Utilisateurs'],
            ['table' => 'articles', 'name' => 'Articles']
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
        //dd($tableNames);
        return $tableNames;
    }

    private function listVars(
        $transactionalVarsRepository,
    )
    {
        $allowedes = [
            'adverts' => 'Annonces',
            'company' => 'Associations / éleveurs', 
            'petitions' => 'Pétitions', 
            'team' => 'Administrateurs', 
            'user' => 'Utilisateurs',
            'articles' => 'Articles'
        ];
        $variables = $transactionalVarsRepository->findAll();
        $tab = [];
        foreach($variables as $var) {
            if(!array_key_exists($var->getVarTable(), $tab)) {
                $tab[$var->getVarTable()] = [
                    'table' => $var->getVarTable(),
                    'name' => $allowedes[$var->getVarTable()],
                    'columns' => [
                        [
                        'columnName' => $var->getVarField(),
                        'comment' => $var->getDescription()
                        ]
                    ]
                ];
            } else {
                $subTab = [
                    'columnName' => $var->getVarField(),
                    'comment' => $var->getDescription()
                ];
                array_push($tab[$var->getVarTable()]['columns'], $subTab);
            }
        }
        return $tab;
    }
}

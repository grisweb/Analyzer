<?php

namespace App\Controller;

use App\Form\AnalyzerType;
use App\Service\Analyzer\Analyzer;
use App\Service\CppAnalyzer;
use App\Service\Lexer\Lexer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param Request $request
     * @param CppAnalyzer $cppAnalyzer
     * @return Response
     */
    public function index(Request $request, CppAnalyzer $cppAnalyzer): Response
    {
        $form = $this->createForm(AnalyzerType::class);

        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid())
            {
                $cppAnalyzer->parseCode($form->get('file')->getData()->getContent());

                return $this->render('result.html.twig', [
                    'form' => $form->createView(),
                    'cppAnalyzer' => $cppAnalyzer,
                ]);
            }
        }

        return $this->render('home.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

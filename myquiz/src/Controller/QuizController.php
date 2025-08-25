<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Form\QuizType;
use App\Repository\CategorieRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class QuizController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(CategorieRepository $categorieRepo): Response
    {
        $categories = $categorieRepo->findAll();

        return $this->render('quiz/home.html.twig', [
            'categories' => $categories
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/quiz/new', name: 'quiz_new')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $user=$this->getUser();
        $quiz = new Quiz();
        $quiz->setUtilisateur($user);
        $form = $this->createForm(QuizType::class, $quiz);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $categorieNom = $form->get('categorie')->getData();
            $categorie = new Categorie();
            $categorie->setNom($categorieNom);
            $quiz->setCategorie($categorie);
            $em->persist($categorie);
            $em->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('quiz/new.html.twig', [
            'formnewquiz' => $form->createView(),
        ]);
    }

    #[Route('/quiz/{categorieId}/{step}', name: 'quiz_step', requirements: ['step' => '\\d+'], defaults: ['step' => 0])]

    public function quiz(
        Request $request,
        $categorieId,
        int $step,
        QuestionRepository $questionRepo
    ): Response {
        $session = $request->getSession();
        $sessionKey = 'quiz_questions_' . $categorieId;

        if (!$session->has($sessionKey)) {
            $questions = $questionRepo->findBy(['categorie' => $categorieId]);

            if (empty($questions)) {
                return $this->render('quiz/no_questions.html.twig', [
                    'message' => "Aucune question disponible pour cette catégorie."
                ]);
            }

            shuffle($questions);
            $questions = array_slice($questions, 0, 15);

            
            foreach ($questions as $question) {
                $question->getReponses()->toArray();
            }

            $session->set($sessionKey, $questions);
            $session->set('score', 0);
        }

        $questions = $session->get($sessionKey);
        $total = count($questions);

        if ($step >= $total) {
            $score = $session->get('score', 0);
            $session->remove($sessionKey);
            $session->remove('score');

            return $this->render('quiz/result.html.twig', [
                'score' => $score,
                'total' => $total
            ]);
        }

        $question = $questions[$step];

    if ($request->isMethod('POST')) {
    $selected = $request->request->get('answer');
    $selectedReponse = null;

    foreach ($question->getReponses() as $reponse) {
        if ($reponse->getId() == $selected) {
            $selectedReponse = $reponse;
            break;
        }
    }

    if ($selectedReponse && $selectedReponse->isEstCorrecte()) {
        $score = $session->get('score', 0);
        $session->set('score', $score + 1);
    }

    return $this->redirectToRoute('quiz_step', [
        'categorieId' => $categorieId,
        'step' => $step + 1
    ]);
}


        return $this->render('quiz/step.html.twig', [
            'question' => $question,
            'step' => $step + 1,
            'total' => $total
        ]);
    }
    
}

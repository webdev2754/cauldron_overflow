<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Repository\AnswerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnswerController extends AbstractController
{
    /**
     * @Route("/answers/{id}/vote", methods="POST", name="answer_vote")
     */
    public function answerVote(
        Answer $answer,
        LoggerInterface $logger,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $data = json_decode($request->getContent(), true);
        $direction = $data['direction'] ?? 'up';

        // todo - use id to query the database

        // use real logic here to save this to the database
        if ($direction === 'up') {
            $logger->info('Voting up!');
//            $currentVoteCount = rand(7, 100);
            $answer->setVotes($answer->getVotes() + 1);
        } else {
            $logger->info('Voting down!');
//            $currentVoteCount = rand(0, 5);
            $answer->setVotes($answer->getVotes() - 1);
        }

        try {
            $entityManager->flush();
        } catch (OptimisticLockException $e) {
        } catch (ORMException $e) {
            $logger->error('Could not save!');
        }

        return $this->json(['votes' => $answer->getVotes()]);
    }

    #[Route('/answers/popular', name: "app_popular_answers")]
    public function popularAnswers(AnswerRepository $answerRepository, Request $request): Response
    {
        $answers = $answerRepository->findMostPopular(
            $request->query->get('q')
        );

        return $this->render('answer/popular_answers.html.twig', [
            'answers' => $answers,
        ]);
    }
}

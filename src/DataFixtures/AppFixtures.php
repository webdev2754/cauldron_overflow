<?php

namespace App\DataFixtures;

use App\Entity\Answer;
use App\Entity\Question;
use App\Factory\QuestionFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        QuestionFactory::createMany(20);

        QuestionFactory::new()
            ->unpublished()
            ->many(5)
            ->create();

        $answer = new Answer();
        $answer->setContent(
            'This question is the best? I wish... I knew the answer.'
        );
        $answer->setUsername('weaverryan');
        $question = new Question();
        $question->setName('How to un-disappear your wallet.');
        $question->setQuestion('... I should not have done this...');
        //setting relation between answer and question-entity by passing the question-object itself (question-record will be referenced via the fk question_id from the answer-table)
        //Doctrine is smart enough to save first question-entity and use its question_id as fk for answer-table

        $answer->setQuestion($question);
        $manager->persist($answer);
        $manager->persist($question);


        $manager->flush();
    }
}

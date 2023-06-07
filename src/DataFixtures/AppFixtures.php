<?php

namespace App\DataFixtures;

use App\Entity\Answer;
use App\Entity\Question;
use App\Factory\AnswerFactory;
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

        //classic way without foundry
        /*$answer = new Answer();
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
        $manager->persist($question);*/

        //The QuestionFactory::random() grabs a random Question from the database.it is now important that we create the questions first and then the answers after.
        //If you want a different value for question_id per Answer, you need to pass a callback function to the second argument instead of an array.
        // That function will then return the array of data to use. Foundry will execute the callback once for each Answer: so 100 times in total.
       /* AnswerFactory::createMany(100, function () {
            return [
                'question' => QuestionFactory::random(),
            ];
        }); */

        AnswerFactory::createMany(100);


        $manager->flush();
    }
}

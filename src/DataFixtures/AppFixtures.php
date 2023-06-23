<?php

namespace App\DataFixtures;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Tag;
use App\Factory\AnswerFactory;
use App\Factory\QuestionFactory;
use App\Factory\TagFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        TagFactory::createMany(100);
        $questions = QuestionFactory::createMany(20, function () {
            return [
                'tags' => TagFactory::randomRange(0, 5),
            ];
        });

        QuestionFactory::new() //get a second instance of question-factory
        ->unpublished(
        ) //->unpublished() to change the default askedAt data.  create a new factory, make everything unpublished and create 5.
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

        //randomly grab only one of the 20 published questions and assign them to answers.
        AnswerFactory::createMany(100, function () use ($questions) {
            return [
                'question' => $questions[array_rand($questions)],
            ];
        });

        /* $question = QuestionFactory::createOne();
         $answer1 = new Answer();
         $answer1->setContent('answer 1');
         $answer1->setUsername('weaverryan');
         $answer2 = new Answer();
         $answer2->setContent('answer 2');
         $answer2->setUsername('weaverryan1');
         $question->addAnswer($answer1);
         $question->addAnswer($answer2);

         $manager->persist($answer1);
         $manager->persist($answer2);*/

        //So this will create 20 new, "needs approval" answers that are set to a random published Question
        AnswerFactory::new(function () use ($questions) {
            return [
                'question' => $questions[array_rand($questions)],
            ];
        })->needsApproval()->many(20)->create();

        //Fix for:   App\Entity\Tag::addQuestion(): Argument #1 ($question) must be of type App\Entity\Question, Zenstruck\Foundry\Proxy given, called in /var/www/project/src/
        //  DataFixtures/AppFixtures.php on line 87  ->object() -> now question is a pure question-object (no foundry-proxy)
        /*  $question = QuestionFactory::createOne()->object();
          $tag1 = new Tag();
          $tag1->setName('dinosaurs');
          $tag2 = new Tag();
          $tag2->setName('monster trucks');
          $question->addTag($tag1); //you can only set in relation via the owning side
          $question->addTag($tag2); //you can only set in relation via the owning side*/

        //experiment, that setting objects in relation via inversed-side does not work
        //you can only set in relation via the owning side
        //this will normally not work, but thanks to smart Entity-Logic it works: $question->addTag($this) in tag->addQuestion-method, the owning-side (Question) will be used again to ensure persisting the  correct manytomany-relation
        //if u comment out $question->addTag($this) it will not work
//        $tag1->addQuestion($question);
//        $tag2->addQuestion($question);

//        $manager->persist($tag1);
//        $manager->persist($tag2);


        $manager->flush();
//        $question->removeTag($tag1);
    }
}

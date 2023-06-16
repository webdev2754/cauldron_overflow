<?php

namespace App\Factory;

use App\Entity\Answer;
use App\Enum\AnswerStatus;
use App\Repository\AnswerRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Answer>
 *
 * @method static Answer|Proxy createOne(array $attributes = [])
 * @method static Answer[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Answer|Proxy find(object|array|mixed $criteria)
 * @method static Answer|Proxy findOrCreate(array $attributes)
 * @method static Answer|Proxy first(string $sortedField = 'id')
 * @method static Answer|Proxy last(string $sortedField = 'id')
 * @method static Answer|Proxy random(array $attributes = [])
 * @method static Answer|Proxy randomOrCreate(array $attributes = [])
 * @method static Answer[]|Proxy[] all()
 * @method static Answer[]|Proxy[] findBy(array $attributes)
 * @method static Answer[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Answer[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static AnswerRepository|RepositoryProxy repository()
 * @method Answer|Proxy create(array|callable $attributes = [])
 */
final class AnswerFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
            'content'   => self::faker()->text(),
            'username'  => self::faker()->userName(),
            'votes'     => self::faker()->randomNumber(),
            'content'   => self::faker()->text(),
            'username'  => self::faker()->userName(),
            'createdAt' => self::faker()->dateTimeBetween('-1 year'),
            'votes'     => rand(-20, 50),
            'status' => AnswerStatus::APPROVED,

            //            'question' => QuestionFactory::random(), //This works because the getdefault-Method will be called 100 times. Each time a new question-id as fk will be generated
            //The problem is subtle (see DataFixtures/AppFixtures.php:49 which overwrites created questions (normally the number should be 25)) ... but maybe you spotted it! We're creating 100 answers... and the getDefaults() method is called for every one.
            // That's.... good! But the moment that this question line is executed, it creates a new unpublished Question and saves it to the database.
            // Then... a moment later, the question is overridden. This means that the 100 answers (see DataFixtures/AppFixtures.php:49 ) were all, in the end,
            // correctly related to one of the 20 published questions.
            // But it also means that, along the way, 100 extra questions were created (see DataFixtures/AppFixtures.php:49 ), saved to the database... then never used.
            //            'question' => QuestionFactory::new()->unpublished()->create(), // This is totally legal: it will create a new unpublished Question, save it to the database and then that Question will be used as the question key when creating the Answer.


            //solution
            //This means that the 'question' (line 65 'question' => ...) key is now set to a QuestionFactory object (but no object-creation at this point).
            //Attention: in DataFixtures/AppFixtures.php:51 the same 'question' key is overwritten (see  'question' => $questions[array_rand($questions)])
            // The new() method returns a new QuestionFactory instance... and then the unpublished() method return self:
            // so it returns that same QuestionFactory object.Setting a relation property to a factory instance is totally allowed.
            // In fact, you should always set a relation property to a factory instance if you can. Why?
            //Because this allows Foundry to delay creating the Question object until later. And in this case, it realizes that
            // the question has been overridden, and so it avoids creating the extra object entirely... which is perfect.

            'question' => QuestionFactory::new()->unpublished(),

        ];
    }

    public function needsApproval():self
    {
        return $this->addState([
            'status' => AnswerStatus::NEEDS_APPROVAL,
        ]);

    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this// ->afterInstantiate(function(Answer $answer) {})
            ;
    }

    protected static function getClass(): string
    {
        return Answer::class;
    }
}

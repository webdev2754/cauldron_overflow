<?php

namespace App\Factory;

use App\Entity\Question;
use App\Repository\QuestionRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Question>
 *
 * @method static Question|Proxy createOne(array $attributes = [])
 * @method static Question[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Question|Proxy find(object|array|mixed $criteria)
 * @method static Question|Proxy findOrCreate(array $attributes)
 * @method static Question|Proxy first(string $sortedField = 'id')
 * @method static Question|Proxy last(string $sortedField = 'id')
 * @method static Question|Proxy random(array $attributes = [])
 * @method static Question|Proxy randomOrCreate(array $attributes = [])
 * @method static Question[]|Proxy[] all()
 * @method static Question[]|Proxy[] findBy(array $attributes)
 * @method static Question[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Question[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static QuestionRepository|RepositoryProxy repository()
 * @method Question|Proxy create(array|callable $attributes = [])
 */
final class QuestionFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    //Foundry "State"
    //this is a self written method
    //Let's try one last thing with Foundry. To have nice testing data, we need a mixture of published and unpublished questions.
    // We're currently accomplishing that by randomly setting some askedAt properties to null.
    // Instead let's create two different sets of fixtures: exactly 20 that are published and exactly 5 that are unpublished.
    public function unpublished(): self
    {
        //Here's the deal: when you call addState(), it changes the default data inside this instance of the factory.
        // Oh, and the return statement here just helps to return self... which allows method chaining.
        return $this->addState(['askedAt' => null]); //askedAt is not set => unpublished
    }

    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->realText(50),
            'question' => self::faker()->paragraphs(
                self::faker()->numberBetween(1, 4),
                true
            ),
            'askedAt' => self::faker()->dateTimeBetween('-100 days', '-1 minute'),
            'votes' => rand(-20, 50),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Question $question) {})
        ;
    }

    /*Doing Things Before Saving
    If you click into one of the questions, you can see that the slug is unique... but was generated in a way that is
    completely unrelated to the question's name.
    That's "maybe" ok... but it's not realistic. To fix this:
     *  Foundry comes with a nice "hook" system where we can do actions before or after each item is saved.
     Inside QuestionFactory, the initialize() method is where you can add these hooks.
     * protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            ->afterInstantiate(function(Question $question) {
                if (!$question->getSlug()) {
                    $slugger = new AsciiSlugger();
                    $question->setSlug($slugger->slug($question->getName()));
                }
            })
            ;
    }*/

    protected static function getClass(): string
    {
        return Question::class;
    }
}

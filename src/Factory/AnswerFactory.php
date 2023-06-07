<?php

namespace App\Factory;

use App\Entity\Answer;
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
            'content' => self::faker()->text(),
            'username' => self::faker()->userName(),
            'votes' => self::faker()->randomNumber(),
            'content' => self::faker()->text(),
            'username' => self::faker()->userName(),
            'createdAt' => self::faker()->dateTimeBetween('-1 year'),
            'votes' => rand(-20, 50),
            'question' => QuestionFactory::random(), //This works because the getdefault-Method will be called 100 times. Each time a new question-id as fk will be generated
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Answer $answer) {})
        ;
    }

    protected static function getClass(): string
    {
        return Answer::class;
    }
}

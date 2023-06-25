<?php

namespace App\Entity;

use App\Enum\AnswerStatus;
use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=QuestionRepository::class)
 */
class Question
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     * @Gedmo\Slug(fields={"name"})
     */
    private $slug;

    /**
     * @ORM\Column(type="text")
     */
    private $question;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $askedAt;

    /**
     * @ORM\Column(type="integer")
     */
    private $votes = 0;

    //extra lazy option means: query only for needed answer-data (e.g. count answers for specific question, triggered in template, see {{ question.answers|length }}), don't fetch all answers each time
    //extra lazy has also disadvantages, be aware of additional (redundant queries)
    /**
     * @ORM\OneToMany(targetEntity=Answer::class, mappedBy="question", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $answers;

    /**
     * @ORM\OneToMany(targetEntity=QuestionTag::class, mappedBy="question")
     */
    private $questionTags;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, inversedBy="questions")
     */
//    private $tags;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
//        $this->tags = new ArrayCollection();
$this->questionTags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getAskedAt(): ?\DateTimeInterface
    {
        return $this->askedAt;
    }

    public function setAskedAt(?\DateTimeInterface $askedAt): self
    {
        $this->askedAt = $askedAt;

        return $this;
    }

    public function getVotes(): int
    {
        return $this->votes;
    }

    public function getVotesString(): string
    {
        $prefix = $this->getVotes() >=0 ? '+' : '-';

        return sprintf('%s %d', $prefix, abs($this->getVotes()));
    }

    public function setVotes(int $votes): self
    {
        $this->votes = $votes;

        return $this;
    }

    public function upVote(): self
    {
        $this->votes++;

        return $this;
    }

    public function downVote(): self
    {
        $this->votes--;

        return $this;
    }

    /**
     * @return Collection|Answer[]
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    //not optimal way for querying all answers and filtering only approved data
   /* public function getApprovedAnswers():Collection
    {
        return $this->answers->filter(function (Answer $answer) {
            return $answer->isApproved();
        });
    }*/

    public function getApprovedAnswers(): Collection
    {
        return $this->answers->matching(AnswerRepository::createApprovedCriteria());
    }

    public function addAnswer(Answer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers[] = $answer;
            $answer->setQuestion($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        if ($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getQuestion() === $this) {
                $answer->setQuestion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    /*public function getTags(): Collection
    {
        return $this->tags;
    }*/

    /*  public function addTag(Tag $tag): self
    {
         if (!$this->tags->contains($tag)) {
             $this->tags[] = $tag;
         }

         return $this;
     }*/

  /*  public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }*/

  /**
   * @return Collection|QuestionTag[]
   */
  public function getQuestionTags(): Collection
  {
      return $this->questionTags;
  }

  public function addQuestionTag(QuestionTag $questionTag): self
  {
      if (!$this->questionTags->contains($questionTag)) {
          $this->questionTags[] = $questionTag;
          $questionTag->setQuestion($this);
      }

      return $this;
  }

  public function removeQuestionTag(QuestionTag $questionTag): self
  {
      if ($this->questionTags->removeElement($questionTag)) {
          // set the owning side to null (unless already changed)
          if ($questionTag->getQuestion() === $this) {
              $questionTag->setQuestion(null);
          }
      }

      return $this;
  }
}

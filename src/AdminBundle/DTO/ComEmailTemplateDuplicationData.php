<?php
namespace AdminBundle\DTO;

use AdminBundle\DTO\DuplicationData;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Doctrine\ORM\EntityManager;
use AdminBundle\Exception\DuplicationSourceNotValidException;

class ComEmailTemplateDuplicationData extends DuplicationData
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        $templateDuplicationSource = $this->em->getRepository('AdminBundle\Entity\ComEmailTemplate')
            ->findOneById($this->duplicationSourceId);
        if (is_null($templateDuplicationSource)) {
            throw new DuplicationSourceNotValidException();
        }
        $templateWithSameName = $this->em->getRepository('AdminBundle\Entity\ComEmailTemplate')
            ->findBy(
                array(
                'name' => $this->name,
                'program' => $templateDuplicationSource->getProgram(),
                )
            );
        if (!empty($templateWithSameName)) {
            $context->buildViolation(self::ERROR_MESSAGE_NAME_ALREADY_USED)
                ->atPath('name')
                ->addViolation();
        }
    }
}

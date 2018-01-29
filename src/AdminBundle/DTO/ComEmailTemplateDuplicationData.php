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
        $template_duplication_source = $this->em->getRepository('AdminBundle\Entity\ComEmailTemplate')
            ->findOneById($this->duplication_source_id);
        if (is_null($template_duplication_source)) {
            throw new DuplicationSourceNotValidException();
        }
        $template_with_same_name = $this->em->getRepository('AdminBundle\Entity\ComEmailTemplate')
            ->findBy(array(
                'name' => $this->name,
                'program' => $template_duplication_source->getProgram(),
            ));
        if (!empty($template_with_same_name)) {
            $context->buildViolation(self::ERROR_MESSAGE_NAME_ALREADY_USED)
                ->atPath('name')
                ->addViolation();
        }
    }
}
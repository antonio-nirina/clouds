<?php
namespace AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AdminBundle\Entity\ProgramUser;

/**
 * Command for creating special user case user
 */
class CreateSpecialUserCaseUserCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setName('app:create-user')
            ->setDescription('Create special use case user.');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        // getting current program
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $programList = $em->getRepository('AdminBundle\Entity\Program')->findAll();
        if (!empty($programList)) {
            $program = $programList[0];

            // creating special user case user
            $userManager = $this->getContainer()->get('fos_user.user_manager');
            $user = $userManager->createUser();
            $user->setName('Bourdais')
                ->setFirstname('Martin')
                ->setUsername('martin_bourdais')
                ->setEmail('martin.bourdais@domain.tld')
                ->setRoles(array('ROLE_ADMIN'));

            $programUser = new ProgramUser();
            $programUser->setAppUser($user)
                ->setProgram($program)
                ->setSpecialUseCaseState(true);

            $userManager->updateUser($user);
            $em->persist($programUser);
            $em->flush();
        }
    }
}
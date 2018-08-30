<?php
/**
 * Created by PhpStorm.
 * User: Hp
 * Date: 22/02/2018
 * Time: 16:41
 */

namespace App\DataFixtures;


use App\Entity\Friend;
use App\Entity\Quiz;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     * @throws
     */
    public function load(ObjectManager $manager)
    {
        $users = [];
        for ($i = 0; $i < 100; $i++) {
            $user = new User();
            $user->setEmail('user@user'.$i.'.com');
            $user->setFirstName('user'.$i);
            $user->setLastName('user'.$i);
            $user->setPhone(md5(uniqid()));
            $password = 'user'.$i;
            $encodedPassword = $this->encoder->encodePassword($user, $password);
            $user->setPassword($encodedPassword);
            $user->setEnabled(true);

            if($i%3 === 0)
            {
                $user->setRoles(['ROLE_PROFESSIONAL']);
            }
            else {
                $user->setRoles(['ROLE_PERSONAL']);
            }
            $nbResponses = rand(1,5);
            $responses = [];
            $quiz = new Quiz();
            $quiz->setQuestion('Question N°'.$i);
            for($j=0;$j<$nbResponses; $j++)
            {
                $response = 'This is the response n°'.$j;
                $responses[] = $response;
            }
            $quiz->setResponses($responses);
            $quiz->setUser($user);
            $manager->persist($quiz);
            $manager->persist($user);


            if($user->hasRole('ROLE_PERSONAL'))
            {
                $users[] = $user;
            }

        }

        for($k = 2; $k<count($users)-3; $k+=2)
        {
            $friend = new Friend();
            $friend->setClassicUser($users[$k-2]);
            $friend->setFriendWith($users[$k-1]);
            $manager->persist($friend);
        }
        $manager->flush();
    }
}
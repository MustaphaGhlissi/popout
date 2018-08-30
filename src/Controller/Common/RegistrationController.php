<?php
/**
 * Created by PhpStorm.
 * User: Hp
 * Date: 20/02/2018
 * Time: 11:14
 */

namespace App\Controller\Common;

use App\Controller\CoreController;
use App\Entity\Quiz;
use App\Entity\User;
use App\Service\QRCodeGenerator;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends CoreController
{
    private $jwtManager;
    public function __construct(JWTManager $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }

    /**
     * @Route("/user/register", name="user_register")
     * @Method("POST")
     */
    public function register(Request $request, QRCodeGenerator $codeGenerator)
    {
        $userManager = $this->get('fos_user.user_manager');
        $entityManager = $this->getDoctrine()->getManager();
        $data = $request->request->all();

        if($userManager->findUserByUsername($data['email']))
        {
            return new JsonResponse(['status'=>false,'message'=>'email used for another account']);
        }
        $user = $userManager->createUser();

        /**
         * @var User $user
         */
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setEmail($data['email']);
        $user->setPhone($data['phone']);
        $user->setPassword($data['password']);
        $user->setPlainPassword($data['password']);
        if(strtolower(trim($data['type'])) === 'role_professional') {
            $user->setRoles(['ROLE_PROFESSIONAL']);
        }
        else
        {
            $user->setRoles(['ROLE_PERSONAL']);
        }

        if(key_exists('questions',$data))
        {
            $questions = $data['questions'];
            $responses = $data['responses'];
            for($i = 0; $i<count($questions); $i++)
            {
                $quiz = new Quiz();
                $quiz->setQuestion($questions[$i]);
                $resps = [];
                if(key_exists($i, $responses))
                {
                    array_push($resps, explode('%', $responses[$i]));
                }
                else
                {
                    array_push($resps, "");
                }
                $quiz->setResponses($resps);
                $quiz->setUser($user);
                $entityManager->persist($quiz);
            }
        }
        $user->setEnabled(true);
        $userManager->updateUser($user);
        $entityManager->flush();
        return $this->generateToken($user);
    }

    protected function generateToken(User $user, $statusCode = 200)
    {
        $token = $this->jwtManager->create($user);
        $response = array(
            'status' => true,
            'token' => $token,
            'message' => 'Inscription effectuée avec succès'
        );
        return new JsonResponse($response, $statusCode);
    }
}
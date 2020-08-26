<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Form\Type\CustomerType;
use App\Form\Type\TaskType;
use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CustomerController extends AbstractController
{
    private $customerRepository;

    private $tokenManager;

    public function __construct(CustomerRepository $customerRepository, CsrfTokenManagerInterface $tokenManager)
    {
        $this->customerRepository = $customerRepository;
        $this->tokenManager = $tokenManager;
    }

    /**
     * @Route("/customer", name="all_customer", methods={"GET"})
     */
    public function index()
    {
        $customers = $this->customerRepository->findAll();

        return $this->render('customer/index.html.twig', ['customers' => $customers]);
    }

    /**
     * @Route("/customer/edit/{id}", name="customer_edit", methods={"GET"})
     */
    public function edit($id, Request $request)
    {
        $customer = $this->customerRepository->findOneBy(['id' => $id]);

        $form = $this->createForm(CustomerType::class, $customer);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $firstName = $request->get('customer')['firstName'];
            $lastName = $request->get('customer')['lastName'];
            $email = $request->get('customer')['email'];
            $phoneNumber = $request->get('customer')['phoneNumber'];

            $submittedToken = $this->tokenManager->getToken('customer')->getValue();

            if ($this->isCsrfTokenValid('customer', $submittedToken)) {

                $customer = $this->customerRepository->findOneBy(['id' => $id]);

                empty($firstName) ? true : $customer->setFirstName($firstName);
                empty($lastName) ? true : $customer->setLastName($lastName);
                empty($email) ? true : $customer->setEmail($email);
                empty($phoneNumber) ? true : $customer->setPhoneNumber($phoneNumber);

                $this->customerRepository->updateCustomer($customer);

                return $this->redirectToRoute('all_customer');
            }
        }

        return $this->render('customer/edit.html.twig', ['customer' => $customer, 'form' => $form->createView()]);
    }

    /**
     * @Route("/customers/", name="add_customer", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $firstName = $request->get('firstName');
        $lastName = $request->get('lastName');
        $email = $request->get('email');
        $phoneNumber = $request->get('phoneNumber');;

        if (empty($firstName) || empty($lastName) || empty($email) || empty($phoneNumber)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        $this->customerRepository->saveCustomer($firstName, $lastName, $email, $phoneNumber);

        return new JsonResponse(['status' => 'Customer created!'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/customers/{id}", name="get_one_customer", methods={"GET"})
     */
    public function get($id): JsonResponse
    {
        $customer = $this->customerRepository->findOneBy(['id' => $id]);

        $data = [
            'id' => $customer->getId(),
            'firstName' => $customer->getFirstName(),
            'lastName' => $customer->getLastName(),
            'email' => $customer->getEmail(),
            'phoneNumber' => $customer->getPhoneNumber(),
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/customers", name="get_all_customers", methods={"GET"})
     */
    public function getAll(): JsonResponse
    {
        $customers = $this->customerRepository->findAll();
        $data = [];

        foreach ($customers as $customer) {
            $data[] = [
                'id' => $customer->getId(),
                'firstName' => $customer->getFirstName(),
                'lastName' => $customer->getLastName(),
                'email' => $customer->getEmail(),
                'phoneNumber' => $customer->getPhoneNumber(),
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }
}

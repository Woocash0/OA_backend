<?php

namespace App\Controller;

use App\Entity\Warranty;

use App\Form\WarrantyFormType;
use App\Repository\WarrantyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;



class WarrantiesController extends AbstractController
{
    private $warrantyRepository;
    private $em;
    public function __construct(WarrantyRepository $warrantyRepository , EntityManagerInterface $em)
    {
        $this->warrantyRepository = $warrantyRepository;
        $this->em = $em;
    }


    #[Route('/warranties', methods:['GET'], name: 'warranties')]
    public function index(Security $security): Response
    {
        $user = $security->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401); // Zwróć błąd, jeśli użytkownik nie jest zalogowany
        }

        // Pobierz wszystkie gwarancje użytkownika
        $warranties = $this->warrantyRepository->findWarrantiesWithTags($user->getId());

        $currentDate = new \DateTime();
        $validWarranties = array_filter($warranties, function($warranty) use ($currentDate) {
        $endDate = clone $warranty->getPurchaseDate();
        $endDate->modify('+ ' . $warranty->getWarrantyPeriod() . ' years');
        return $endDate >= $currentDate;
        });

        return new JsonResponse([
            'warranties' => array_map(function ($warranty) {
                return [
                    'id' => $warranty->getId(),
                    'category' => $warranty->getCategory(),
                    'productName' => $warranty->getProductName(),
                    'purchaseDate' => $warranty->getPurchaseDate()->format('Y-m-d'), // Format daty
                    'warrantyPeriod' => $warranty->getWarrantyPeriod(),
                    'tags' => array_map(function ($tag) {
                        return $tag->getName(); // Lista nazw tagów
                    }, $warranty->getTags()->toArray()),
                ];
            }, $validWarranties),
        ], 200); // Odpowiedź z kodem 200 (OK)
    }
    

    #[Route('/add_warranty', name: 'add_warranty')]
    public function addWarranty(Request $request): Response
    {
        $user = $this->getUser();
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        
        
        $warranty = new Warranty();
        $form = $this->createForm(WarrantyFormType::class, $warranty);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $newWarranty = $form->getData();
            $newWarranty->setIdUser($user);

            $receipt = $form->get('receipt')->getData();

            if($receipt){
                $newFileName = uniqid() . '.' . $receipt->guessExtension();

                try{
                    $receipt->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFileName
                    );
                } catch(FileException $e){
                    return new Response($e->getMessage());
                }

                $newWarranty->setReceipt($newFileName);
            }else{
                $newWarranty->setReceipt('no-image.svg');
            }

            $selectedTags = $form->get('tags')->getData();
            foreach ($selectedTags as $selectedTag) {
                $newWarranty->addTag($selectedTag);
            }
            
            $this->em->persist($newWarranty);
            $this->em->flush();

            return $this->redirectToRoute('warranties');
        }
        
        return $this->render('/views/dashboard/add_warranty.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/edit_warranty/{id}', name: 'edit_warranty')]
    public function editWarranty($id, Request $request, Security $security): Response {
        $user = $security->getUser();

        $warranty = $this->warrantyRepository->find($id);

        if (!$warranty) {
            throw $this->createNotFoundException('Gwarancja o podanym identyfikatorze nie istnieje.');
        }

        if ($warranty->getIdUser()->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException('Nie masz uprawnień do edycji tej gwarancji.');
        }

        $form = $this->createForm(WarrantyFormType::class, $warranty);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $receipt = $form->get('receipt')->getData();

            if ($receipt) {
                if ($warranty->getReceipt() !== null) {
                    $this->getParameter('kernel.project_dir') . $warranty->getReceipt();
                    $newFileName = uniqid() . '.' . $receipt->guessExtension();
                        try{
                            $receipt->move(
                                $this->getParameter('kernel.project_dir') . '/public/uploads',
                                $newFileName
                            );
                        } catch(FileException $e){
                            return new Response($e->getMessage());
                        }
                        $warranty->setReceipt($newFileName);
                        $this->em->flush();
                        return $this->redirectToRoute('warranties');
                } else {
                    dd('jest nullem');
                }
            } else {
                $warranty->setCategory($form->get('category')->getData());
                $warranty->setProductName($form->get('product_name')->getData());
                $warranty->setPurchaseDate($form->get('purchase_date')->getData());
                $warranty->setWarrantyPeriod($form->get('warranty_period')->getData());

                $tags = $form->get('tags')->getData();

                foreach ($tags as $tag) {
                    $warranty->addTag($tag);
                }

                $this->em->flush();

                return $this->redirectToRoute('warranties');
            }
        }

        return $this->render('/views/dashboard/edit_warranty.html.twig', [
            'warranty' => $warranty,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/delete_warranty/{id}', methods:['GET','DELETE'], name: 'delete_warranty')]
    public function deleteWarranty($id): Response {

        $warranty = $this->warrantyRepository->find($id);

        $this->em->remove($warranty);
        $this->em->flush();

        return $this->redirectToRoute('warranties');
    }

    #[Route('/search', methods: ['POST'])]
    public function search(Request $request): Response
    {
        $content = json_decode($request->getContent(), true);

        if (!isset($content['search'])) {
            return $this->json(['error' => 'Invalid request'], 400);
        }

        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $searchString = '%'.strtolower($content['search']).'%';

        $query = $this->warrantyRepository->createQueryBuilder('w')
            ->andWhere('w.idUser = :id')
            ->andWhere('LOWER(w.category) LIKE :search OR LOWER(w.productName) LIKE :search')
            ->setParameter('id', $user->getId())
            ->setParameter('search', $searchString)
            ->getQuery();

        $searched = $query->getResult();
        
        return $this->json(
            $searched,
            headers: ['Content-Type' => 'application/json;charset=UTF-8']
        );
    }

    #[Route('/archive', methods:['GET'], name: 'archive')]
    public function archive(Security $security): Response
    {
        $user = $security->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $warranties = $this->warrantyRepository->findBy(['idUser' => $user->getId()]);

        $currentDate = new \DateTime();
        $expiredWarranties = array_filter($warranties, function($warranty) use ($currentDate) {
            $endDate = clone $warranty->getPurchaseDate();
            $endDate->modify('+ ' . $warranty->getWarrantyPeriod() . ' years');
            return $endDate < $currentDate;
        });

        return $this->render('/views/dashboard/archive.html.twig', [
           'archives' => $expiredWarranties
            ]);
}

    #[Route('/account', name: 'account')]
    public function showAccount(): Response{
    
            $user = $this->getUser();
            $userDetails = $user->getIdUserDetails();
            
            return $this->render('/views/account/account.html.twig', [
            'userDetails' => $userDetails,
        ]);
    }

}
<?php

namespace App\Controller;

use App\Entity\AddProductHistory; 
use App\Entity\Product;
use App\Form\AddProductHistoryType;
use App\Form\ProductType;
use App\Repository\AddProductHistoryRepository;
use App\Repository\ProductRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/editor')]
#[IsGranted('ROLE_ADMIN')] #cette annotation indique que toutes les méthodes de ce contrôleur nécessitent que l'utilisateur ait le rôle ROLE_ADMIN pour y accéder. Cela signifie que seules les utilisateurs ayant ce rôle pourront accéder aux actions définies dans ce contrôleur, telles que l'affichage de la liste des produits, la création d'un nouveau produit, la modification d'un produit existant, etc. Si un utilisateur qui n'a pas le rôle ROLE_ADMIN tente d'accéder à ces actions, il sera redirigé vers une page d'erreur ou une page de connexion en fonction de la configuration de sécurité de l'application.
final class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product_index', methods: ['GET'])] #route pour afficher la liste des produits
    public function index(ProductRepository $productRepository): Response #cette méthode affiche la liste des produits. Elle prend en paramètre le repository des produits, qui est automatiquement injecté par Symfony grâce à l'autowiring. La méthode utilise le repository pour récupérer tous les produits de la base de données en appelant la méthode findAll(). Ensuite, elle rend la vue 'product/index.html.twig' en passant la collection de produits à la vue pour qu'elle puisse afficher les informations de chaque produit dans un tableau.
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(), #on utilise le repository pour récupérer tous les produits de la base de données en appelant la méthode findAll(). Cela nous permet d'obtenir une collection de produits que nous pouvons ensuite passer à la vue pour qu'elle puisse afficher les informations de chaque produit dans un tableau.
        ]);
    }
#region Add
    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]#route pour ajouter un nouveau produit
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response #cette méthode permet de créer un nouveau produit. Elle prend en paramètre la requête HTTP, l'entity manager pour gérer les entités et le slugger pour générer des noms de fichiers sûrs pour les images téléchargées.
    {
        $product = new Product(); #on crée une nouvelle instance de l'entité Product, qui représente le produit que nous souhaitons ajouter à la base de données. Cette instance est utilisée pour stocker les données du nouveau produit avant de les enregistrer en base de données.
        $form = $this->createForm(ProductType::class, $product); #on crée un formulaire à partir de la classe ProductType, qui est un formulaire personnalisé pour créer un nouveau produit. Ce formulaire est lié à l'entité Product, ce qui signifie que les données saisies dans le formulaire seront automatiquement mappées à l'entité du produit que nous souhaitons créer.
        $form->handleRequest($request); #on traite la requête pour le formulaire, ce qui permet de vérifier si le formulaire a été soumis et de récupérer les données saisies par l'utilisateur. Si le formulaire a été soumis, les données seront automatiquement mappées à l'entité du produit grâce à la liaison entre le formulaire et l'entité.

        if ($form->isSubmitted() && $form->isValid()) { #si le formulaire a été soumis et que les données sont valides, on procède à la création du nouveau produit
            $image = $form->get('image')->getData();//! on recup l'image et son contenu
   
            if ($image) {/*si l'image existe*/
                $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME); // Nom d'origine de l'image
                $safeImageName = $slugger->slug($originalName);/* permet de recup des image avec espace dans le nom et l'enlever*/
                $newFileImageName = $safeImageName.'-'.uniqid().'.'.$image->guessExtension();/*cree un id unique a toute les images meme si elles ont un nom similaire*/

                try { // On tente de déplacer le fichier physiquement sur le serveur
                    $image->move
                        ($this->getParameter('image_directory'), // getParameter, crée un dossier et envoie le à cet endroit là ('dans services.yaml')
                        $newFileImageName);/* on recup l'image et on la renomme et on la stocke dans le repoertoire */
                }catch (FileException $exception) {}/*en cas d'erreur -> Si le déplacement échoue, on arrive ici*/
                    $product->setImages($newFileImageName); // set(nouveau nom image)
                
            }

            $entityManager->persist($product); // 
            $entityManager->flush();

            $stockHistory = new AddProductHistory();/*nouvelle instanciation de la classe*/
            $stockHistory->setQuantity($product->getStock());/*on recup l'id du produit et on ajoute au stock*/
            $stockHistory->setProduct($product);/*on insere le produit*/
            $stockHistory->setCreatedAt(new DateTimeImmutable());
            $entityManager->persist($stockHistory);
            $entityManager->flush();/*effectue la mise a jour en bdd*/
            
            $this->addFlash('success','Votre produit a été ajouté');
            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }
#endregion 
#region Show
    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]#route pour afficher les détails d'un produit
    public function show(Product $product): Response#cette méthode affiche les détails d'un produit spécifique. Elle prend en paramètre l'entité du produit à afficher, qui est automatiquement injectée par Symfony grâce à la correspondance de l'id dans la route. La méthode rend ensuite la vue 'product/show.html.twig' en passant l'entité du produit à la vue pour qu'elle puisse afficher les informations du produit.
    {
        return $this->render('product/show.html.twig', [#on rend la vue 'product/show.html.twig' en passant l'entité du produit à la vue pour qu'elle puisse afficher les informations du produit.
            'product' => $product,#on passe à la vue l'entité du produit à afficher pour qu'elle puisse être utilisée dans le template, par exemple pour afficher le nom, la description, le prix, etc. du produit.
        ]);
    }
#endregion
#region Edit
    #[Route('/product/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]#route pour modifier un produit
    public function edit(Request $request, Product $product, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);#on crée un formulaire à partir de la classe ProductType, qui est un formulaire personnalisé pour modifier les produits. Ce formulaire est lié à l'entité Product, ce qui signifie que les données saisies dans le formulaire seront automatiquement mappées à l'entité du produit que nous souhaitons modifier.
        $form->handleRequest($request);#on traite la requête pour le formulaire, ce qui permet de vérifier si le formulaire a été soumis et de récupérer les données saisies par l'utilisateur.

        if ($form->isSubmitted() && $form->isValid()) { #si le formulaire a été soumis et que les données sont valides, on procède à la création du nouveau produit
            $image = $form->get('image')->getData();//! on recup l'image et son contenu
   
            if ($image) {/*si l'image existe*/
                $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME); // Nom d'origine de l'image
                $safeImageName = $slugger->slug($originalName);/* permet de recup des image avec espace dans le nom et l'enlever*/
                $newFileImageName = $safeImageName.'-'.uniqid().'.'.$image->guessExtension();/*cree un id unique a toute les images meme si elles ont un nom similaire*/

                try { // On tente de déplacer le fichier physiquement sur le serveur
                    $image->move
                        ($this->getParameter('image_directory'), // getParameter, crée un dossier et envoie le à cet endroit là ('dans services.yaml')
                        $newFileImageName);/* on recup l'image et on la renomme et on la stocke dans le repoertoire */
                }catch (FileException $exception) {}/*en cas d'erreur -> Si le déplacement échoue, on arrive ici*/
                    $product->setImages($newFileImageName); // set(nouveau nom image)
                
            }

            
            $entityManager->flush();
            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/edit.html.twig', [#on rend la vue du formulaire de modification du produit. On passe à la vue l'entité du produit à modifier et le formulaire lui-même pour qu'il puisse être affiché dans la page.
            'product' => $product,#on passe à la vue l'entité du produit à modifier pour qu'elle puisse être utilisée dans le template, par exemple pour pré-remplir les champs du formulaire avec les données actuelles du produit.
            'form' => $form,#on passe à la vue le formulaire lui-même pour qu'il puisse être affiché dans la page. Le formulaire contiendra les champs nécessaires pour modifier les informations du produit, et il sera lié à l'entité du produit pour que les données saisies soient automatiquement mappées à l'entité lors de la soumission du formulaire.
        ]);
    }
#endregion
#region Delete
    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]#route pour supprimer un produit
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response #cette méthode permet de supprimer un produit existant. Elle prend en paramètre la requête HTTP, l'entité du produit à supprimer et l'entity manager pour gérer les entités.
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->getString('_token'))) {#on vérifie si le token CSRF est valide pour éviter les attaques CSRF. Le token est généré en utilisant l'id du produit et doit être inclus dans le formulaire de suppression.
            $entityManager->remove($product);#on utilise l'entity manager pour supprimer le produit de la base de données. La méthode remove() marque l'entité pour suppression, mais la suppression effective ne se produit que lorsque flush() est appelé.
            $entityManager->flush();#on effectue la suppression en base de données en appelant la méthode flush() de l'entity manager. Cela exécute toutes les opérations en attente, y compris la suppression du produit.
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/add/product/{id}/', name:'app_product_stock', methods: ['GET','POST'])]#route pour ajouter du stock a un produit
    public function addStock(Request $request, EntityManagerInterface $entityManager, ProductRepository $productRepository,$id): Response #cette méthode permet d'ajouter du stock à un produit existant. Elle prend en paramètre la requête HTTP, l'entity manager pour gérer les entités, le repository pour accéder aux produits(ici find()) et l'id du produit auquel on souhaite ajouter du stock.
    {   
        
        $stockAdd = new AddProductHistory();#on crée une nouvelle instance de la classe AddProductHistory qui représente l'historique des ajouts de stock pour un produit. Cette classe est utilisée pour enregistrer les quantités ajoutées au stock d'un produit ainsi que la date de l'ajout.
        $form = $this->createForm(AddProductHistoryType::class, $stockAdd);#on crée un formulaire à partir de la classe AddProductHistoryType, qui est un formulaire personnalisé pour ajouter du stock à un produit. Ce formulaire est lié à l'entité AddProductHistory, ce qui signifie que les données saisies dans le formulaire seront automatiquement mappées à l'entité.
        $form->handleRequest($request);#on traite la requête pour le formulaire, ce qui permet de vérifier si le formulaire a été soumis et de récupérer les données saisies par l'utilisateur.

        $product = $productRepository->find($id);#on utilise le repository pour trouver le produit correspondant à l'id passé en paramètre. Cela nous permet de récupérer l'entité du produit auquel nous voulons ajouter du stock.
        if($form->issubmitted() && $form->isvalid()) {

            if($stockAdd->getQuantity() >0){ #si la quantité saisie est supérieure à 0, on ajoute le stock au produit}
                $newQuantity = $product->getStock() + $stockAdd->getQuantity(); #on recup la quantité actuelle du produit et on lui ajoute la quantité saisie dans le formulaire
                $product->setStock($newQuantity);#on met a jour le stock du produit avec la nouvelle quantité
                $stockAdd->setcreatedAt(new DateTimeImmutable());#on enregistre la date de l'ajout de stock en utilisant la classe DateTimeImmutable pour obtenir la date et l'heure actuelles. Cela nous permet de suivre l'historique des ajouts de stock pour le produit.
                $stockAdd->setProduct($product);#on associe l'entité AddProductHistory à l'entité du produit en utilisant la méthode setProduct(). Cela permet de lier l'historique des ajouts de stock au produit correspondant, ce qui est important pour suivre les modifications de stock dans le temps.

                $entityManager->persist($stockAdd);#on persiste le produit pour enregistrer les modifications du stock
                $entityManager->flush();#on effectue la mise à jour en bdd

                $this->addFlash('success','Le stock du produit a été modifié');#on ajoute un message flash de succès pour informer l'utilisateur que le stock a été modifié
                return $this->redirectToRoute('app_product_index');#on redirige l'utilisateur vers la liste des produits après avoir ajouté le stock
            }else{
                $this->addFlash('error','La quantité doit être supérieure à 0');#si la quantité saisie est inférieure ou égale à 0, on affiche un message d'erreur pour informer l'utilisateur que la quantité doit être supérieure à 0
                return $this->redirectToRoute('app_product_stock', ['id'=>$product ->getId()]);#on redirige l'utilisateur vers le formulaire d'ajout de stock pour le même produit afin qu'il puisse saisir une quantité valide
            }

    
        }

        return $this->render('product/addStock.html.twig', #on rend la vue du formulaire d'ajout de stock pour le produit. On passe à la vue l'entité du produit et le formulaire lui-même pour qu'il puisse être affiché dans la page.
        ['form'=> $form->createView(), #on passe à la vue le formulaire lui-même pour qu'il puisse être affiché dans la page. Le formulaire contiendra les champs nécessaires pour saisir la quantité de stock à ajouter, et il sera lié à l'entité AddProductHistory pour que les données saisies soient automatiquement mappées à l'entité lors de la soumission du formulaire.
        'product' => $product, #on passe à la vue l'entité du produit pour qu'elle puisse être utilisée dans le template, par exemple pour afficher le nom du produit ou d'autres informations pertinentes dans la page d'ajout de stock.
        ]
        );
    }
    #[Route('/add/product/{id}/stock/history', name:'app_product_stock_add_history', methods: ['GET','POST'])]
    public function showHistoryProductStock($id, ProductRepository $productRepository,AddProductHistoryRepository $productHistoryRepository): Response
    {
        $product = $productRepository->find($id);#on utilise le repository pour trouver le produit correspondant à l'id passé en paramètre. Cela nous permet de récupérer l'entité du produit pour lequel nous voulons afficher l'historique des ajouts de stock.
        $stockHistory = $productHistoryRepository->findBy(['product'=>$product], ['id'=>'DESC']);#on utilise le repository pour trouver tous les enregistrements d'historique des ajouts de stock associés au produit en utilisant la méthode findBy(). On spécifie le critère de recherche en utilisant un tableau associatif où la clé est 'product' et la valeur est l'entité du produit que nous avons récupérée précédemment. De plus, on spécifie un ordre de tri pour les résultats en utilisant un autre tableau associatif où la clé est 'id' et la valeur est 'DESC', ce qui signifie que les résultats seront triés par identifiant dans l'ordre décroissant (du plus récent au plus ancien).
        
        return $this->render('product/showHistory.html.twig', [#on rend la vue 'product/historyStock.html.twig' en passant l'entité du produit à la vue pour qu'elle puisse afficher les informations du produit et son historique des ajouts de stock.
           'stockHistories' => $stockHistory,#on passe à la vue l'historique des ajouts de stock pour qu'il puisse être affiché dans le template.
        
        
        ]);
    }
}
#endregion
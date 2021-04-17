<?php

namespace App\Controller;
use App\Entity\Photo;
use App\Entity\Userr;
use App\Form\PhotoAddFormType;
use App\Repository\PhotoRepository;
use App\Repository\UserrRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;



class PhotoController extends AbstractController
{
    /**
     * @Route("/photo", name="photo")
     */
    public function index(): Response
    {
        return $this->render('photo/index.html.twig', [
            'controller_name' => 'PhotoController',
        ]);
    }

    /**
     * @Route ("/photo/main")
     */
    public function index1(): Response
    {
        return $this->render('photo/Main.html.twig', [
            'controller_name' => 'PhotoController',
        ]);
    }

    /**
     * @Route("/photo/gallerie", name="photoGall")
     * @return Response
     */
    public function Gallerie()
    {
        return $this->render('photo/Gallerie.html.twig', [
            'controller_name' => 'PhotoController',
        ]);
    }

    /**
     * @Route("/photo/add",name="recherche_nsc")
     * @param Request $req
     * @param UserrRepository $urep
     * @param PhotoRepository $prep
     * @return Response
     */
    public function addPhoto(Request $req, UserrRepository $urep,PhotoRepository $prep): Response
    {
        $Pics=$prep->findAll();
        $users=$urep->find(24);
        $Photo = new Photo();
        $form = $this->createForm(PhotoAddFormType::class, $Photo);
        $Photo->setIdu($users);
        $Photo->setDateAjout(date("Y/m/d"));
        $form->handleRequest($req);
        if($form->isSubmitted()&& $form->isValid()){
            $file = $form['url']->getData();
            $directory=".\assets\images";
            $file->move($directory, $file->getClientOriginalName());
            $Photo->setUrl($directory."\\".$file->getClientOriginalName());

            $Photo->setCouleur($form['couleur']->getData());
            $Photo->setTheme($form['theme']->getData());
            $Photo->setLocalisation($form['localisation']->getData());
            $em = $this->getDoctrine()->getManager();
            $em->persist($Photo);
            $em->flush();
            $Pics=$prep->findAll();
        }
        return $this->render('photo/Gallerie.html.twig',array(
            'tab' => $Pics,
            'f1' => $form->createView(),
        ));

    }



    /**
     * @param PhotoRepository $rep
     * @param Request $req
     * @return Response
     * @Route ("/photo/discover",name="rechercheP")
     */
    public function Search(PhotoRepository $rep, Request $req){
        $data=$req->get('tfrech');
        $photos=$rep->searchPhoto($data);
        return $this->render("photo/Discover.html.twig",["tab"=>$photos]);


    }

    /**
     * @param PhotoRepository $prep
     * @param $id
     * @param Request $req
     * @return Response
     * @Route ("/photo/showOne/{id}",name="ShowPic")
     */
    public function ShowPhoto(PhotoRepository $prep, $id, Request $req){
        $Photo=$prep->find($id);

        $form = $this->createFormBuilder($Photo)
            ->add('titre')
            ->add('theme')
            ->add('couleur',ColorType::class,[
                'attr' => ['class' => 'form-control form-control-color'],
            ])
            ->add('localisation')
            ->add('Modifier', SubmitType::class,[
                'attr' => ['class' => 'btn btn-primary'],
            ])
        ->getForm();
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {

            $em=$this->getDoctrine()->getManager();

            $em->flush($Photo);

            return $this->redirectToRoute('rechercheP');
        }

        return $this->render("photo/ShowPhoto.html.twig",array(
            "pic"=>$Photo,
            'f1' => $form->createView(),
        ));
    }

    /**
     * @param PhotoRepository $prep
     * @param $id
     * @param Request $req
     * @return Response
     * @Route ("/photo/DeleteOne/{id}",name="DeletePic")
     */
    public function DeletePhoto(PhotoRepository $prep, $id, Request $req){
        $Photo=$prep->find($id);
        $Pics=$prep->findAll();

        $em=$this->getDoctrine()->getManager();
        $em->remove($Photo);
        $em->flush();



        return $this->redirectToRoute('recherche_nsc');
    }
}
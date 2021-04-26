<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Repository\CommentaireRepository;
use App\Repository\PhotoRepository;
use App\Repository\UserrRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Test\Constraint\RequestAttributeValueSame;
use Symfony\Component\Routing\Annotation\Route;

class CommentaireController extends AbstractController
{
    /**
     * @Route("/commentaire", name="commentaire")
     */
    public function index(): Response
    {
        return $this->render('commentaire/index.html.twig', [
            'controller_name' => 'CommentaireController',
        ]);
    }

    /**
     * @param PhotoRepository $prep
     * @param $id
     * @param CommentaireRepository $crep
     * @param UserrRepository $urep
     * @param Request $req
     * @return Response
     * @Route ("commentaire/comment/{id}",name="comment")
     */
    public function ShowComments(PhotoRepository $prep,UserrRepository $urep, $id,CommentaireRepository $crep,Request $req){
        $Photo=$prep->find($id);
        $users=$urep->find(24);
        $comments=$crep->myComments($users->getIdu(),$id);
        $allomms=$crep->allComments($users->getIdu(),$id);
        return $this->render("photo/commenter.html.twig",array(
            'pic' => $Photo,
            'comms' => $comments,
            'otherComms'=>$allomms,
        ));
    }

    /**
     * @param UserrRepository $urep
     * @param PhotoRepository $crep
     * @param CommentaireRepository $crep
     * @param Request $req
     * @param $id
     * @return Response
     * @Route ("/commentaire/comm/{id}",name="comme")
     */
    public function Commenter(UserrRepository $urep,PhotoRepository $prep,CommentaireRepository $crep,Request $req,$id){
        $data=$req->get('tfcomment');
        $users=$urep->find(25);
        $Commentaire = new Commentaire();
        $Commentaire->setComm($data);
        $Commentaire->setIdPhoto($id);
        $Commentaire->setIdu($users->getIdu());
        $Commentaire->setNomUser($users->getNom() . "  " . $users->getPrenom());
        $em=$this->getDoctrine()->getManager();
        $em->persist($Commentaire);
        $em->flush();
        $Photo=$prep->find($id);



        return $this->redirect($req->server->get('HTTP_REFERER'));


    }

    /**
     * @param CommentaireRepository $crep
     * @param PhotoRepository $prep
     * @param UserrRepository $urep
     * @param $id
     * @param $idp
     * @param Request $req
     * @return Response
     * @Route ("/commentaire/delete/{id}/{idp}",name="delcomm")
     */

    public function DeleteComment(CommentaireRepository $crep,UserrRepository $urep,PhotoRepository $prep, $id, $idp, Request $req){

        $Comment=$crep->find($id);
        $users=$urep->find(25);
        $em=$this->getDoctrine()->getManager();
        $em->remove($Comment);
        $em->flush();



        $Photo=$prep->find($idp);


        return $this->redirect($req->server->get('HTTP_REFERER'));
    }

    /**
     * @param PhotoRepository $prep
     * @param CommentaireRepository $crep
     * @param UserrRepository $urep
     * @param $id
     * @param $idp
     * @param Request $req
     * @return Response
     * @Route ("/commentaire/modif/{id}/{idp}",name="modifcomm")
     */
    public function modifComment(PhotoRepository $prep,UserrRepository $urep,CommentaireRepository $crep, $id,$idp, Request $req){
        $Comment=$crep->find($id);
        $data=$req->get('tfmodif');
        $Comment->setComm($data);
      $em=$this->getDoctrine()->getManager();

            $em->flush($Comment);
        $Photo=$prep->find($idp);

        $users=$urep->find(25);

        return $this->redirect($req->server->get('HTTP_REFERER'));
    }


    /**
     * @param CommentaireRepository $crep
     * @param Request $req
     * @param $id
     * @return Response
     * @Route ("commentaire/commentback/{id}",name="commentback")
     */
    public function ShowCommentsBack(CommentaireRepository $crep,$id,Request $req){
        $comments=$crep->backComments($id);

        return $this->render("commentaire/Commentaireback.html.twig",[ "comments"=>$comments]);
    }

    /**
     * @param CommentaireRepository $crep
     * @param $id
     * @param Request $req
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route ("/commentaire/deleteback/{id}",name="delback")
     */
    public function DeleteCommentBack(CommentaireRepository $crep,$id,Request $req){

        $Comment=$crep->find($id);
        $em=$this->getDoctrine()->getManager();
        $em->remove($Comment);
        $em->flush();


        return $this->redirect($req->server->get('HTTP_REFERER'));
    }
}

<?php

use RedBeanPHP\R;

class KitchenController extends BaseController
{
    public function index()
    {
        // shows list of kitchens
        $kitchens = R::findAll('kitchens');
        render('kitchens/index.twig', array(
            'entities' => $kitchens
        ));
    }
    public function show($id)
    {
        // show one kitchen
        if ($id) {
            $typeOfBean = 'kitchens';
            $queryStringKey = $id;
            $kitchen = $this->getBeanById($typeOfBean, $queryStringKey);
            // get recipes
            $recipes = R::find('recipes', ' kitchens_id = ? ', [$queryStringKey]);
            if ($kitchen) {
                render('kitchens/show.twig', array(
                    'item' => $kitchen,
                    'recipes' => $recipes,
                ));
            } else {
                throw new Exception('No kitchen with ID ' . $id . ' found');
            }
        } else {
            throw new Exception('No kitchen ID specified');
        }
    }
    public function create()
    {
        // check if user is logged in
        $this->authorizeUser();

        // create new kitchen
        render('kitchens/create.twig');
    }
    public function createPost()
    {
        // check if user is logged in
        $this->authorizeUser();

        if (isset($_POST['name'])) {
            $NewKitchen = R::dispense('kitchens');
            $NewKitchen->name = $_POST['name'];
            $NewKitchen->description  = $_POST['description'];

            $id = R::store($NewKitchen);
            $this->show($id);
        } else {
            throw new Exception('No new post found');
        }
    }
    public function edit($id)
    {
        // check if user is logged in
        $this->authorizeUser();

        if ($id) {
            $typeOfBean = 'kitchens';
            $queryStringKey = $id;
            $kitchen = $this->getBeanById($typeOfBean, $queryStringKey);
            render('kitchens/edit.twig', array(
                'item' => $kitchen,
            ));
        }
    }
    public function editPost($id)
    {
        // check if user is logged in
        $this->authorizeUser();

        if ($id) {
            $EditKitchen = R::load('kitchens', $id);
            $EditKitchen->name = $_POST['name'];
            $EditKitchen->description = $_POST['description'];

            $id = R::store($EditKitchen);
            $this->show($id);
        } else {
            throw new Exception('No post found');
        }
    }
}

<?php

use RedBeanPHP\R;

class RecipeController extends BaseController
{
    private const TYPES = [
        'breakfast',
        'lunch',
        'dinner',
        'desert',
        'snack'
    ];
    private const LEVELS = [
        'easy',
        'medium',
        'hard',
        'expert'
    ];



    public function index()
    {
        $recipes = R::findAll('recipes');
        render('recipes/index.twig', array(
            'entities' => $recipes
        ));
    }
    public function show($id)
    {
        if ($id) {
            $typeOfBean = 'recipes';
            $queryStringKey = $id;
            $recipe = $this->getBeanById($typeOfBean, $queryStringKey);
            if ($recipe) {
                render('recipes/show.twig', array(
                    'recipe' => $recipe
                ));
            } else {
                throw new Exception('No recipe with ID ' . $id . ' found');
            }
        } else {
            throw new Exception('No recipe ID specified');
        }
    }
    public function create()
    {
        // check if user is logged in
        $this->authorizeUser();

        $kitchens = R::find('kitchens');
        render('recipes/create.twig', array(
            'types' => self::TYPES,
            'levels' => self::LEVELS,
            'kitchens' => $kitchens,
        ));
    }
    public function createPost()
    {
        // check if user is logged in
        $this->authorizeUser();

        if (isset($_POST['name'])) {
            $NewRecipe = R::dispense('recipes');
            $NewRecipe->name = $_POST['name'];
            $NewRecipe->type = $_POST['type'];
            $NewRecipe->level = $_POST['level'];
            // one-to-many relation
            $KitchenRecipes = R::find('kitchens', ' name = ? ', [$_POST['kitchen']]);
            foreach ($KitchenRecipes as $KitchenRecipe) {
                $KitchenRecipe->ownRecipeList[] = $NewRecipe;
                R::store($KitchenRecipe);
            }
            R::store($NewRecipe);
            $id = $NewRecipe['kitchens_id'];
            $KitchenController = new KitchenController();
            $KitchenController->show($id);
        } else {
            throw new Exception('No new post found');
        }
    }
    public function edit($id)
    {
        // check if user is logged in
        $this->authorizeUser();

        if ($id) {
            $typeOfBean = 'recipes';
            $queryStringKey = $id;
            $recipe = $this->getBeanById($typeOfBean, $queryStringKey);
            $kitchens = R::find('kitchens');
            render('recipes/edit.twig', array(
                'recipe' => $recipe,
                'types' => self::TYPES,
                'levels' => self::LEVELS,
                'kitchens' => $kitchens,
            ));
        }
    }
    public function editPost($id)
    {
        // check if user is logged in
        $this->authorizeUser();

        if ($id) {
            $EditRecipe = R::load('recipes', $id);
            $EditRecipe->name = $_POST['name'];
            $EditRecipe->type = $_POST['type'];
            $EditRecipe->level = $_POST['level'];
            // one-to-many relation
            $KitchenRecipes = R::find('kitchens', ' name = ? ', [$_POST['kitchen']]);
            foreach ($KitchenRecipes as $KitchenRecipe) {
                $KitchenRecipe->ownRecipeList[] = $EditRecipe;
                R::store($KitchenRecipe);
            }

            $id = R::store($EditRecipe);
            $this->show($id);
        } else {
            throw new Exception('No post found');
        }
    }
}

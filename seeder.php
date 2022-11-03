<?php

use RedBeanPHP\R as R;

require '../vendor/autoload.php';

$db   = 'foodblog';
$user = 'root';
$pass = "";

R::setup('mysql:host=127.0.0.1', $user, $pass);
R::exec("CREATE DATABASE IF NOT EXISTS $db");
R::exec("use $db");

// array of recipes
$recipes = [
    [
        'name'  => 'Pannekoeken',
        'type'  => 'dinner',
        'level' => 'easy',
        'kitchen' => 'Hollandse keuken',
    ],
    [
        'name'  => 'Tosti',
        'type'  => 'lunch',
        'level' => 'easy',
        'kitchen' => 'Franse keuken',
    ],
    [
        'name'  => 'Boeren ommelet',
        'type'  => 'lunch',
        'level' => 'easy',
        'kitchen' => 'Franse keuken',
    ],
    [
        'name'  => 'Broodje Pulled Pork',
        'type'  => 'lunch',
        'level' => 'hard',
        'kitchen' => 'Mediterraans',
    ],
    [
        'name'  => 'Hutspot met draadjesvlees',
        'type'  => 'dinner',
        'level' => 'medium',
        'kitchen' => 'Hollandse keuken',
    ],
    [
        'name'  => 'Nasi Goreng met Babi ketjap',
        'type'  => 'dinner',
        'level' => 'hard',
        'kitchen' => 'Chineese keuken',
    ],
];

// array of kitchens
$kitchens = [
    [
        'name' => 'Franse keuken',
        'description' => 'De Franse keuken is een internationaal gewaardeerde keuken met een lange traditie. Deze 
        keuken wordt gekenmerkt door een zeer grote diversiteit, zoals dat ook wel gezien wordt in de Chinese 
        keuken en Indische keuken.',
    ],
    [
        'name' => 'Chineese keuken',
        'description' => 'De Chinese keuken is de culinaire traditie van China en de Chinesen die in de diaspora 
        leven, hoofdzakelijk in Zuid-Oost-Azië. Door de grootte van China en de aanwezigheid van vele volkeren met 
        eigen culturen, door klimatologische afhankelijkheden en regionale voedselbronnen zijn de variaties groot.',
    ],
    [
        'name' => 'Hollandse keuken',
        'description' => 'De Nederlandse keuken is met name geïnspireerd door het landbouwverleden van Nederland.
         Alhoewel de keuken per streek kan verschillen en er regionale specialiteiten bestaan, zijn er voor 
         Nederland typisch geachte gerechten. Nederlandse gerechten zijn vaak relatief eenvoudig en voedzaam, 
         zoals pap, Goudse kaas, pannenkoek, snert en stamppot.',
    ],
    [
        'name' => 'Mediterraans',
        'description' => 'De mediterrane keuken is de keuken van het Middellandse Zeegebied en bestaat onder 
        andere uit de tientallen verschillende keukens uit Marokko,Tunesie, Spanje, Italië, Albanië en Griekenland 
        en een deel van het zuiden van Frankrijk (zoals de Provençaalse keuken en de keuken van Roussillon).',
    ],

];

// array of user
$user = [

    'username' => 'future-teck-leader',
    'password' => 'password123'

];

// insert kitchens into table
$kitchenTable = R::findAll('kitchens');
if (!$kitchenTable) {
    $KitchenBeans = R::dispense('kitchens', count($kitchens));

    foreach ($kitchens as $index => $kitchen) {
        $KitchenBeans[$index]->name = $kitchen['name'];
        $KitchenBeans[$index]->description = $kitchen['description'];
    }
    R::storeAll($KitchenBeans);
}


// insert recipes into table
$recipeTable = R::findAll('recipes');
if (!$recipeTable) {
    $RecipeBeans = R::dispense('recipes', count($recipes));

    foreach ($recipes as $index => $recipe) {
        $RecipeBeans[$index]->name = $recipe['name'];
        $RecipeBeans[$index]->type = $recipe['type'];
        $RecipeBeans[$index]->level = $recipe['level'];
        // one-to-many relation
        $KitchenRecipes = R::find('kitchens', ' name = ? ', [$recipe['kitchen']]);
        foreach ($KitchenRecipes as $KitchenRecipe) {
            $KitchenRecipe->ownRecipeList[] = $RecipeBeans[$index];
            R::store($KitchenRecipe);
        }
    }
    R::storeAll($RecipeBeans);
}

// insert user into table
$userTable = R::findAll('user');
if (!$userTable) {
    $UserBeans = R::dispense('user');

    $UserBeans->username = $user['username'];
    $UserBeans->password = password_hash($user['password'], PASSWORD_DEFAULT);
    R::store($UserBeans);
}

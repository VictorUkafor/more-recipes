<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {

        // signup route
        Route::post('signup', 'UserController@signup')
        ->middleware('validateSignup');

        // login route
        Route::post('login', 'UserController@login')
        ->middleware('validateLogin');

        //user details route
        Route::get('user', 'UserController@details')
        ->middleware('auth:api');

        Route::group([
            'namespace' => 'Auth',
            'middleware' => 'api',
            'prefix' => 'password-reset'
        ], function () {
            
            Route::post('request', 'PasswordResetController@create')
            ->middleware('validateUser');
            
            Route::get('find/{token}', 'PasswordResetController@find');
            
            
            Route::post('reset', 'PasswordResetController@reset')
            ->middleware('validatePasswordReset');
        });
    });

    // recipes
    Route::prefix('recipes')->group(function () {
        Route::middleware(['auth:api'])->group(function () {
            
            // saves a recipe
            Route::post('/', 'RecipeController@store')
            ->middleware('validateNewRecipe');

            Route::middleware(['findRecipe'])->group(function () {
                
                Route::middleware(['ownRecipe'])->group(function () {
                    
                    // update a recipe
                    Route::put('/{recipeId}', 'RecipeController@update')
                    ->middleware('validateUpdateRecipe');

                    // soft deletes a recipe
                    Route::delete('/{recipeId}', 'RecipeController@softDelete');
                });
                
                // post a reaction
                Route::post('/{recipeId}/reaction', 'ReactionController@post');

                // post a favourite
                Route::post('/{recipeId}/favourite', 'FavouriteController@post');
            });
        });

        // show all recipes
        Route::get('/', 'RecipeController@showAll')
        ->middleware('findAllRecipes');

        // show a recipe
        Route::get('/{recipeId}', 'RecipeController@show')
        ->middleware('findRecipe');
    });
});

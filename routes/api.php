<?php

use App\Http\Controllers\Api\ArticleCommentController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\FavoritesController;
use App\Http\Controllers\Api\FilterController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\PriceHistoryController;
use App\Http\Controllers\Api\RouteController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\SkuController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\SkuVideoController;
use App\Http\Controllers\Api\SocialController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\TrackingController;
use App\Http\Controllers\Api\UserController;



use App\Http\Controllers\Api\Supplier\AuthController as SupplierAuthController;
use App\Http\Controllers\Api\Supplier\StoreController as SupplierStoreController;



use App\Http\Controllers\Api\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Api\Admin\ArticleCommentController as AdminArticleCommentController;
use App\Http\Controllers\Api\Admin\SkuController as AdminSkuController;
use App\Http\Controllers\Api\Admin\StoreController as AdminStoreController;
use App\Http\Controllers\Api\Admin\IngredientController as AdminIngredientController;
use App\Http\Controllers\Api\Admin\CountryController as AdminCountryController;
use App\Http\Controllers\Api\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\Api\Admin\RatingController as AdminRatingController;
use App\Http\Controllers\Api\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Api\Admin\QuestionController as AdminQuestionController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\TrackingController as AdminTrackingController;
use App\Http\Controllers\Api\Admin\TagController as AdminTagController;


use App\Http\Controllers\Api\Admin\VisitStaticticsController;
use App\Http\Controllers\Api\Admin\LinkClickController;
use App\Http\Controllers\Api\Admin\SitemapController;
use App\Http\Controllers\Api\Admin\SupplierController;
use App\Http\Controllers\Api\Admin\Parser\LinkOptionController;
use App\Http\Controllers\Api\Admin\Parser\LinkParserController;
use App\Http\Controllers\Api\Admin\Parser\ParsingLinkController;
use App\Http\Controllers\Api\Admin\Parser\PriceOptionController;
use App\Http\Controllers\Api\Admin\Parser\PriceParserController;
use App\Http\Controllers\Api\Admin\Parser\ProductOptionController;
use App\Http\Controllers\Api\Admin\Parser\ProductParserController;
use App\Http\Controllers\Api\Admin\Parser\Old\ParserControllerProxy;
use Illuminate\Support\Facades\Route;

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
//Route::middleware('throttle:5,1')->group(function () {
//
//});

//Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail']);
Route::get('/login/{service}', [SocialController::class, 'redirect']);
Route::get('/login/{service}/callback', [SocialController::class, 'callback']);

Route::group(['prefix' => '/admin'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum', 'role:admin']);
});


Route::post('/supplier/signin', [App\Http\Controllers\Api\Supplier\AuthController::class, 'login']);
Route::post('/supplier/signup', [App\Http\Controllers\Api\Supplier\AuthController::class, 'register']);
Route::post('/supplier/logout', [SupplierAuthController::class, 'logout'])->middleware(['auth:sanctum', 'role:supplier']);



Route::post('/add-to-tracking', [TrackingController::class, 'addToTracking']);
Route::post('/supplier/register', [AuthController::class, 'register']);
Route::post('/supplier/login', [AuthController::class, 'login']);
Route::post('/subscription', [SubscriptionController::class, 'store']);


Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/last', [ArticleController::class, 'last']);
Route::get('/articles/by-tag/{tag}', [ArticleController::class, 'byTag']);
Route::get('/articles/by-slug/{slug}', [ArticleController::class, 'show']);



Route::get('/banner', [BannerController::class, 'index']);
Route::get('/filter', [FilterController::class, 'index']);
Route::get('filter/receipts', [FilterController::class, 'receipts']);
Route::get('/price-history', [PriceHistoryController::class, 'index']);

Route::get('/brands/by-code/{code}', [BrandController::class, 'byCode']);
Route::get('/brands/popular', [BrandController::class, 'popular']);
Route::get('/brands/all', [BrandController::class, 'index']);


Route::get('/categories/by-code/{code}', [CategoryController::class, 'byCode']);
Route::get('/categories/nested', [CategoryController::class, 'nested']);


Route::get('/show-compared-skus', [SkuController::class, 'showComparedSkus']);
Route::get('/skus/by-sku-id/{skuId}', [SkuController::class, 'bySkuId']);
Route::get('/skus/main', [SkuController::class, 'mainIndex']);


Route::get('/suggest', [SearchController::class, 'index']);

Route::get('/skus/viewed', [SkuController::class, 'viewed']);

Route::get('/reviews/by-sku-id/{id}', [ReviewController::class, 'bySkuId']);
Route::get('/reviews/additional-info-by-sku-id/{id}', [ReviewController::class, 'additionalInfoBySkuId']);

Route::get('/comments', [CommentController::class, 'byReviewId']);

Route::get('/questions', [QuestionController::class, 'bySkuId']);

Route::post('/rating/check-user-rating', [RatingController::class, 'checkUserRating']);
Route::post('/rating/create-or-update', [RatingController::class, 'createOrUpdate']);


Route::get('/routes/skus-queries', [RouteController::class, 'skusQueries']);
Route::get('/routes/articles', [RouteController::class, 'articles']);
Route::get('/routes/categories', [RouteController::class, 'categories']);
Route::get('/routes/brands', [RouteController::class, 'brands']);

Route::post('/images', [ImageController::class, 'store']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/articles/my', [ArticleController::class, 'my']);
    Route::get('/articles/categories', [ArticleController::class, 'articleCategories']);
    Route::get('/articles/tags', [ArticleController::class, 'tags']);

    Route::get('/article-comments/my', [ArticleCommentController::class, 'my']);
    Route::post('/article-comments', [ArticleCommentController::class, 'store']);
    Route::delete('/article-comments/{id}', [ArticleCommentController::class, 'destroy']);

    Route::get('/comments/my', [CommentController::class, 'my']);
    Route::post('/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);

    Route::get('/videos/my', [SkuVideoController::class, 'my']);


    Route::get('/reviews/my', [ReviewController::class, 'my']);
    Route::post('/review/check-existing-review', [ReviewController::class, 'checkExistingReview']);
    Route::post('/reviews', [ReviewController::class, 'updateOrCreate']);
    Route::post('/reviews/add-video', [SkuVideoController::class, 'addOrUpdateVideo']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);

    Route::get('/questions/my', [QuestionController::class, 'my']);
    Route::post('/questions', [QuestionController::class, 'store']);
    Route::delete('/questions/{id}', [QuestionController::class, 'destroy']);

    Route::get('/favorites', [FavoritesController::class, 'index']);
    Route::get('/show-favorite-skus', [FavoritesController::class, 'showFavoriteSkus']);
    Route::post('/favorites', [FavoritesController::class, 'store']);
    Route::delete('/favorites/{id}', [FavoritesController::class, 'destroy']);

    Route::get('/me', [UserController::class, 'me']);
    Route::post('/users/me', [UserController::class, 'updateMe']);

    //доступ только у админа
    Route::group([
        'middleware' => ['role:admin'],
        'prefix' => '/admin'
    ], function () {

        Route::prefix('/parser')->group(function () {

            Route::get('/save-brand', [ParserControllerProxy::class, 'saveBrand']);
            Route::get('/save-category', [ParserControllerProxy::class, 'saveCategory']);
            Route::get('/save-country', [ParserControllerProxy::class, 'saveCountry']);
            Route::get('/save-link', [ParserControllerProxy::class, 'saveLink']);
            Route::get('/parser/save-product', [ParserControllerProxy::class, 'saveProduct']);
            Route::get('/save-sku', [ParserControllerProxy::class, 'saveSku']);
            Route::get('/save-price-history', [ParserControllerProxy::class, 'savePriceHistory']);
            Route::get('/save-current-price', [ParserControllerProxy::class, 'saveCurrentPrice']);
            Route::get('/save-store', [ParserControllerProxy::class, 'saveStore']);
            Route::get('/save-ingredient', [ParserControllerProxy::class, 'saveIngredient']);
            Route::get('/save-ingredient-product', [ParserControllerProxy::class, 'saveIngredientProduct']);

            Route::get('/parsed-links', [ParsingLinkController::class, 'index']);
            Route::get(
                '/parsed-links/stores-with-unparsed-links-count',
                [ParsingLinkController::class, 'storesWithUnparsedLinksCount']
            );
            Route::get(
                '/parsed-links/stores-with-links-count',
                [ParsingLinkController::class, 'storesWithLinksCount']
            );

            Route::get('/product-option', [ProductOptionController::class, 'index']);
            Route::post('/product-option', [ProductOptionController::class, 'updateOrCreate']);


            Route::get('/price-option', [PriceOptionController::class, 'index']);
            Route::post('/price-option', [PriceOptionController::class, 'updateOrCreate']);

            Route::get('/link-option', [LinkOptionController::class, 'index']);
            Route::post('/link-option', [LinkOptionController::class, 'updateOrCreate']);

            Route::post('/product/parse-product-by-link-ids', [ProductParserController::class, 'parseProductByLinkIds']);

            Route::post('/price/parse-prices-by-link-ids', [PriceParserController::class, 'parsePricesByLinkIds']);
            Route::post(
                '/price/parse-prices-from-actual-price-parsing-table',
                [PriceParserController::class, 'parsePricesFromActualPriceParsingTable']
            );

            Route::post('/link/parse-links', [LinkParserController::class, 'parseLinks']
            );
        });


        Route::get('/sitemap', [SitemapController::class, 'create']);


        //Route::delete('/images/{image}',[ImageController::class, 'destroy']);


        Route::get('/articles/categories', [AdminArticleController::class, 'articleCategories']);
        Route::post('/articles/publish/{id}', [AdminArticleController::class, 'publish']);
        Route::post('/articles/withdraw-from-publication/{id}', [AdminArticleController::class, 'withdrawFromPublication']);
        Route::apiResource('/articles', AdminArticleController::class);
        Route::get('/tags/tree', [AdminTagController::class, 'tree']);
        Route::apiResource('/tags', AdminTagController::class);
        Route::apiResource('/skus', AdminSkuController::class);
        Route::apiResource('/brands', AdminBrandController::class);
        Route::apiResource('/categories', AdminCategoryController::class);

        Route::get('/ingredients/show-available-active-ingredients-groups', [AdminIngredientController::class, 'showAvailableActiveIngredientsGroups']);
        Route::apiResource('/ingredients', AdminIngredientController::class);


        Route::apiResource('/countries', AdminCountryController::class);

        Route::post('/stores/change-price-parsing-status', [AdminStoreController::class, 'changePriceParsingStatus']);
        Route::post('/stores/change-checking-image-count-status', [AdminStoreController::class, 'changeCheckingImageCountStatus']);
        Route::apiResource('/stores', AdminStoreController::class);


        Route::get('/suppliers', [SupplierController::class, 'index']);
        Route::post('/suppliers/set-status/{id}', [SupplierController::class, 'setStatus']);

        Route::post('/reviews/set-status/{id}', [AdminReviewController::class, 'setStatus']);
        Route::get('/reviews/dynamics', [AdminReviewController::class, 'dynamics']);
        Route::apiResource('/reviews', AdminReviewController::class);

        Route::post('/questions/set-status/{id}', [AdminQuestionController::class, 'setStatus']);
        Route::get('/questions', [AdminQuestionController::class, 'index']);



        Route::post('/comments/set-status/{id}', [AdminCommentController::class, 'setStatus']);
        Route::apiResource('/comments', AdminCommentController::class);

        Route::post('/article-comments/set-status/{id}', [AdminArticleCommentController::class, 'setStatus']);
        Route::apiResource('/article-comments', AdminArticleCommentController::class);

        Route::get('/users/show-available-roles', [AdminUserController::class, 'showAvailableRoles']);
        Route::apiResource('/users', AdminUserController::class);

        Route::get('/trackings', [AdminTrackingController::class, 'index']);
        Route::get('/trackings/dynamics', [AdminTrackingController::class, 'dynamics']);
        Route::get('/rating/dynamic', [AdminRatingController::class, 'dynamic']);
        Route::get('/visit-statistics', [VisitStaticticsController::class, 'index']);

        Route::get('/links/by-store', [LinkClickController::class, 'clicksByStore']);
        Route::get('/links/by-date', [LinkClickController::class, 'index']);
    });



    //доступ только у поставщика
    Route::group([
        'middleware' => ['role:supplier'],
        'prefix' => '/supplier'
    ], function () {
        Route::post('/stores/add-price-file/{id}', [SupplierStoreController::class, 'addPriceFile']);
        Route::post('/stores', [SupplierStoreController::class, 'store']);
        Route::put('/stores/{id}', [SupplierStoreController::class, 'update']);
        Route::get('/stores', [SupplierStoreController::class, 'index']);
        Route::get('/stores/my', [SupplierStoreController::class, 'my']);
    });

});




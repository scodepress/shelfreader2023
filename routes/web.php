<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */


Route::group(['prefix' => 'admin'], function () {
	Voyager::routes();
});

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

Route::post('/forgot-password', function(Request $request) {
	$request->validate(['email' => 'required|email']);
$status = Password::sendResetLink(
        $request->only('email')
);
	return $status === Password::RESET_LINK_SENT
                ? back()->with(['status' => __($status)])
                : back()->withErrors(['email' => __($status)]);

})->middleware('guest')->name('password.email');

Route::get('register.step2', 'RegisterStep2Controller@show')->name('register.step2');
Route::post('register.step2', 'RegisterStep2Controller@store')->name('register.step2.store');

Route::get('/', function () {
		return Inertia::render('Welcome', [
			'canLogin' => Route::has('login'),
			'canRegister' => Route::has('register'),
			'laravelVersion' => Application::VERSION,
			'phpVersion' => PHP_VERSION,
		]);
	});
Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');
Route::group(['middleware'=>['is_approved_user']], function() {

Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
	    ])->setRememberToken(Str::random(60));
            $user->save();
            event(new PasswordReset($user));
        }
    );

       return $status === Password::PASSWORD_RESET
                ? redirect()->route('login')->with('status', __($status))
                : back()->withErrors(['email' => [__($status)]]);
})->middleware('guest')->name('password.update'); 
	
Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
	return Inertia::render('Dashboard');
})->name('dashboard');
	Route::group(['auth:sanctum'], function() {

		Route::group(['middleware'=>['is_local_admin']], function() {

			Route::get('local-admin','LocalAdminController@index')->name('local-admin');

		});

		Route::group(['middleware'=>['is_super_user']], function() {

			Route::get('super.admin/{user?}/{institution?}','AdminController@show')->name('super.admin');
			Route::put('approve-members', 'AdminController@approveMembers')->middleware('auth:sanctum')
								  ->name('approve-members');
			Route::put('approve-users', 'ApproveUserController@store')->middleware('auth:sanctum')->name('approve-users');
			Route::delete('delete-user', 'AdminController@destroy')->middleware('auth:sanctum')->name('delete-user');
			Route::get('super.admin/impersonate', 'Admin\ImpersonateController@index')->name('admin.impersonate');
			Route::post('super.admin/impersonate', 'Admin\ImpersonateController@store');
			Route::get('super.admin/impersonate/destroy', 'Admin\ImpersonateController@destroy');
			Route::post('/users.impersonate', 'UsersController@impersonate')->name('users.impersonate');

		});

		Route::get('/leave-impersonate', 'UsersController@leaveImpersonate')->name('leave-impersonate');

		Route::middleware(['auth:sanctum', 'verified'])
			->get('/shelf', [App\Http\Controllers\ShelvesController::class, 'show'])->name('shelf');

		Route::middleware(['auth:sanctum', 'verified'])->get('/imp','ApiController@index')->name('imp]');
		Route::middleware(['auth:sanctum', 'verified'])->get('/shelfreader-inventory','ShelfreaderInventoryController@show')
						 ->name('shelfreader-inventory');
		Route::post('/process-barcode','FolioRequestController@processFolio')->name('process-barcode');
		Route::post('/load.demo','FolioRequestController@processFolio')->name('load.demo');
		Route::post('save.candidate.to.shelf','ShelfCandidateController@saveCandidateToShelf')->name('save.candidate.to.shelf');
		Route::get('/check_book/{barcode}/{user_id}/{service}', 'ShelvesController@checkBook')->middleware('auth:sanctum')
											->name('check_book');
		Route::get('master.shelf/{sortSchemeId}/{clear}', 'MasterShelfController@show')->middleware('auth:sanctum')
										 ->name('master.shelf');
		Route::post('delete.item', 'ShelvesController@delete')->middleware('auth:sanctum')->name('delete.item');
		Route::delete('first.scan', 'FirstScanController@delete')->middleware('auth:sanctum')->name('first.scan');
		Route::delete('shelf.candidate/{barcode}', 'ShelfCandidateController@destroy')
			->middleware('auth:sanctum')
			->name('shelf.candidate');
		Route::post('store.candidate', 'ShelfCandidateController@store')->middleware('auth:sanctum')->name('store.candidate');

		Route::get('/maps','MapController@show')->middleware('auth:sanctum')->name('maps');
		Route::post('process-folio-inventory', 'FolioInventoryRequestController@processFolio')
			->middleware('auth:sanctum')->name('process-folio-inventory');
		Route::get('process-alma/{barcode}/{user_id}', 'AlmaRequestController@processAlma')->middleware('auth:sanctum')
										     ->name('process-alma');
		Route::post('empty_tables', 'ShelvesController@truncate')->middleware('auth:sanctum')->name('empty_tables');
		Route::post('empty_inventory_tables', 'ShelfreaderInventoryController@truncate')->middleware('auth:sanctum')
										  ->name('empty_inventory_tables');
		Route::get('correction/{user_id}/{barcode}', 'ShelfCorrectionController@correction')
			->middleware('auth:sanctum')->name('correction');

		Route::post('inventory-correction', 'FolioInventoryRequestController@correction')->middleware('auth:sanctum')
										   ->name('inventory-correction');
		Route::post('choose-sort', 'SortSchemeController@put')->middleware('auth:sanctum')->name('choose-sort');
		Route::post('store-token', 'FolioAuthenticationTokenController@store')->middleware('auth:sanctum')->name('store-token');
		Route::post('create-token', 'FolioAuthenticationTokenController@adminCreateToken')->middleware('auth:sanctum')->name('create-token');
		Route::post('update-auth-token', 'FolioAuthenticationTokenController@adminUpdateToken')
			->middleware('auth:sanctum')->name('update-auth-token');
		Route::get('inventory-report','LocalInventoryReportController@show')->name('inventory-report');
		Route::get('mdewey','MissouriDeweySortKeyController@show')->name('mdewey');
		Route::get('maps','MapController@show')->name('maps');
		//Route::post('inventory.search', 'MasterShelfController@searchInventory')->name('inventory.search');
		Route::post('result.search', 'MasterShelfController@searchInventory')->name('result.search');
		Route::post('inventory.callnumbers', 'MasterShelfController@searchCallNumbers')->name('inventory.callnumbers');
		Route::get('export/{dateFileFormat}', 'MasterShelfController@export')->name('export');
		Route::get('export.master/{fileFormat}/{sortSchemeId}', 
			'MasterShelfController@exportMasterShelf')
			->name('export.callnumbers');
		Route::get('clear.search', 'MasterShelfController@clearSearch')->name('clear.search');
		Route::get('instructions', 'InstructionController@show')->name('instructions');
		Route::get('alerts', 'AlertController@show')->name('alerts');
		Route::get('library', 'LibraryController@show')->name('library');

		Route::get('update', 'UpdateController@show')->name('update');


		Route::get('update.users', 'UpdateController@loadUsersTable')->name('update.users');
		Route::get('load.ias', 'UpdateController@loadLccInstitutionApiServices')->name('load.ias');
		Route::get('load.alerts', 'UpdateController@loadAlerts')->name('load.alerts');
		Route::get('load.ems', 'UpdateController@getEmsItems')->name('load.ems');
		Route::get('load.engineering', 'UpdateController@getEngineeringItems')->name('load.engineering');
		Route::get('create.map.user', 'UpdateController@createMapUsers')->name('create.map.user');
		Route::get('load.shelves.table', 'UpdateController@fillShelvesTable')->name('load.shelves.table');


	});
});



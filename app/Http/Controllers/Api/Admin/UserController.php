<?php

namespace App\Http\Controllers\Api\Admin;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProvider;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    use DataProvider;

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) ($request->per_page ?? 10);

        if ($perPage === -1) {
            $users = User::select(['id', 'name'])->get();
            return response()->json(['data' => $users]);
        }

        $query = DB::table('users')
            ->select([
                'users.id as id',
                'users.name as name',
                'users.email as email',
                'users.created_at as created_at',
                'users.service as service',
                'roles.name as role',
                'user_infos.avatar as avatar'
            ])
            ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
            ->leftJoin('user_infos', 'user_infos.user_id', '=', 'users.id');

        $result = $this->prepareModel($request, $query, true)->paginate( $perPage);

        return response()->json(['data' => $result]);
    }

    /**
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showAvailableRoles(): JsonResponse
    {
        $result = Role::select('id', 'name')->get();
        return response()->json(['data' => $result]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $result = User::select('id', 'email', 'name', 'role_id')->where('id', $id)->first();
        return response()->json(['data' => $result ]);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $user): JsonResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     */
    public function destroy(int $id): void
    {
        User::destroy($id);
    }
}

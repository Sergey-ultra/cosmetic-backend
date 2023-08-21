<?php

namespace App\Http\Controllers\Api\Admin;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DataProvider;
use App\Http\Requests\Admin\BotsRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\UserInfo;
use App\Repositories\MessageRepository\MessageRepository;
use App\Services\AuthService;
use App\Services\PasswordService\PasswordService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    use DataProvider;

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) ($request->per_page ?? 10);

        if ($perPage === -1) {
            $users = User::query()->select(['id', 'name'])->get();
            return response()->json(['data' => $users]);
        }

        $query = User::query()
            ->select([
                sprintf( '%s.id', User::TABLE),
                sprintf( '%s.name', User::TABLE),
                sprintf( '%s.email', User::TABLE),
                sprintf( '%s.avatar', UserInfo::TABLE),
                sprintf( '%s.role_id', User::TABLE),
                sprintf( '%s.service', User::TABLE),
                sprintf( '%s.balance', User::TABLE),
                sprintf( '%s.referral_balance', User::TABLE),
                sprintf( '%s.ref', User::TABLE),
                sprintf( '%s.referral_owner', User::TABLE),
                sprintf( '%s.created_at', User::TABLE),
            ])
            ->leftJoin(
                UserInfo::TABLE,
                sprintf('%s.user_id', UserInfo::TABLE),
                '=',
                sprintf('%s.id', User::TABLE)
            )
            ->orderByDesc(sprintf('%s.created_at', User::TABLE))
        ;

        $result = $this->prepareModel($request, $query, true)->paginate( $perPage);


        return response()->json(['data' => $result]);
    }


    public function my(MessageRepository $messageRepository): JsonResponse
    {
        $unreadMessageCount = $messageRepository->unreadFeedbackMessagesCount();
        return response()->json(['data' => [
            'unreadMessageCount' => $unreadMessageCount
        ]]);
    }

    public function showAvailableRoles(): JsonResponse
    {
        $result = [];
        foreach (User::ROLE_MAP_NAME as $roleId => $roleName) {
            $result[] = [
                'id' => $roleId,
                'name' => $roleName,
            ];
        }

        return response()->json(['data' => $result]);
    }

    public function getMasterPassword(PasswordService $passwordService): JsonResponse
    {
        return response()->json(['data' => $passwordService->generateGlobalMasterPassword()]);
    }

    public function saveBots(BotsRequest $request, AuthService $authService): JsonResponse
    {
        $bots = $request->input('bots');
        $errors = [];

        foreach ($bots as $bot) {
            $createdUser = $authService->saveUser($bot['email'], $bot['name'], $bot['password'], User::ROLE_BOT);

            if (is_null($createdUser)) {
                $errors[$bot['email']] = sprintf(' Юзер с email %s уже существует', $bot['email']);
            }
        }

        return response()->json(['data' => [
            'status' => count($bots) === count($errors) ? 'fails' : 'success',
            'errors' => $errors
        ]]);
    }

    public function store(Request $request): JsonResponse
    {
        //
    }

    public function show(int $id): JsonResponse
    {
        $result = User::select('id', 'email', 'name', 'role_id')->where('id', $id)->first();
        return response()->json(['data' => $result ]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        //
    }

    public function destroy(int $id): void
    {
        User::destroy($id);
    }
}

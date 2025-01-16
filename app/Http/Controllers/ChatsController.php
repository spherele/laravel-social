<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat; // Модель для чатов
use App\Models\Message; // Модель для сообщений
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

// Для работы с авторизацией

class ChatsController extends Controller
{
    /**
     * Получить список чатов для текущего пользователя.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): Response
    {
        return Inertia::render('Chats/Index', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Получить сообщения для конкретного чата.
     *
     * @param int $chatId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMessages($chatId)
    {

        $chat = Chat::findOrFail($chatId);
        $user = Auth::user();

        if (!$chat->users->contains($user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messages = $chat->messages()->with('user')->get();

        return response()->json($messages);
    }

    /**
     * Отправить сообщение в чат.
     *
     * @param Request $request
     * @param int $chatId
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request, $chatId)
    {

        $request->validate([
            'text' => 'required|string|max:1000',
        ]);

        $chat = Chat::findOrFail($chatId);
        $user = Auth::user();

        if (!$chat->users->contains($user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message = new Message([
            'text' => $request->input('text'),
            'user_id' => $user->id,
        ]);

        $chat->messages()->save($message);

        return response()->json($message->load('user'), 201);
    }

    /**
     * Создать новый чат.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createChat(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        // Создаем новый чат
        $chat = new Chat();
        $chat->save();

        // Привязываем пользователей к чату
        $chat->users()->attach($request->input('user_ids'));
        $chat->users()->attach(Auth::id()); // Добавляем текущего пользователя

        return response()->json($chat->load('users'), 201);
    }
}

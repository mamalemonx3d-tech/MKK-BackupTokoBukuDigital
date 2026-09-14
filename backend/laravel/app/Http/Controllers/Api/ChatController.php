<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatResource;
use App\Models\Chat;
use App\Models\ChatDeletion;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    // Admin view: list users who have chat history, or User view: get isolated conversation
    public function index(Request $request): JsonResponse
    {
        if ($request->user()->role !== 'admin') {
            $adminId = $request->query('admin_id');
            return $this->getConversationForUser($request, $request->user()->id, $adminId ? (int)$adminId : null);
        }

        // Admin view: Get users with recent chat
        $userIds = Chat::select('user_id')
            ->groupBy('user_id')
            ->pluck('user_id');

        $users = User::whereIn('id', $userIds)
            ->where('role', 'user')
            ->get()
            ->map(function ($user) {
                $lastChat = Chat::where('user_id', $user->id)->latest()->first();
                $unread = Chat::where('user_id', $user->id)
                    ->where('sender', 'user')
                    ->where('is_read', false)
                    ->count();

                return [
                    'id'              => $user->id,
                    'name'            => $user->name,
                    'username'        => $user->username,
                    'foto'            => $user->foto ? asset('storage/' . $user->foto) : null,
                    'last_message'    => $lastChat ? $lastChat->pesan : '',
                    'last_message_id' => $lastChat ? $lastChat->id : 0,
                    'last_sender'     => $lastChat ? $lastChat->sender : '',
                    'last_time'       => $lastChat ? $lastChat->created_at->format('H:i') : '',
                    'unread'          => $unread,
                ];
            });

        return response()->json(['data' => $users]);
    }

    /**
     * GET /api/chats/unread-count
     * Counter pesan unread untuk floating widget & nav bottom
     */
    public function getUnreadCount(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            $count = Chat::where('sender', 'user')
                ->where(function ($q) {
                    $q->where('is_read', 0)->orWhere('is_read', false)->orWhereNull('is_read');
                })
                ->count();
        } else {
            $count = Chat::where('user_id', $user->id)
                ->where('sender', 'admin')
                ->where(function ($q) {
                    $q->where('is_read', 0)->orWhere('is_read', false)->orWhereNull('is_read');
                })
                ->count();
        }

        return response()->json(['unread_count' => (int)$count]);
    }

    /**
     * POST /api/chats/mark-as-read
     * Tandai semua pesan sebagai dibaca
     */
    public function markAsRead(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            $targetUserId = $request->input('user_id');
            $query = Chat::where('sender', 'user')->where('is_read', false);
            if ($targetUserId) {
                $query->where('user_id', $targetUserId);
            }
            $query->update(['is_read' => true]);
        } else {
            Chat::where('user_id', $user->id)->where('sender', 'admin')->where('is_read', false)->update(['is_read' => true]);
        }

        return response()->json(['message' => 'Pesan ditandai sebagai dibaca']);
    }

    // Fetch conversation for a specific user_id and optional admin_id
    public function getConversation(Request $request, int $userId): JsonResponse
    {
        if ($request->user()->role !== 'admin' && $request->user()->id !== $userId) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $adminId = $request->query('admin_id');
        return $this->getConversationForUser($request, $userId, $adminId ? (int)$adminId : null);
    }

    protected function getConversationForUser(Request $request, int $userId, ?int $adminId = null): JsonResponse
    {
        $currentUserId = $request->user()->id;

        $query = Chat::with(['user', 'admin'])->where('user_id', $userId);

        if ($adminId) {
            $query->where(function ($q) use ($adminId) {
                $q->where('admin_id', $adminId)->orWhereNull('admin_id');
            });
        }

        // Exclude messages this user has hidden for themselves
        $query->whereNotExists(function ($sub) use ($currentUserId) {
            $sub->from('chat_deletions')
                ->whereColumn('chat_deletions.chat_id', 'chats.id')
                ->where('chat_deletions.user_id', $currentUserId);
        });

        $chats = $query->orderBy('created_at', 'asc')->get();

        return response()->json([
            'data' => ChatResource::collection($chats),
        ]);
    }

    // Send a message
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pesan'    => 'required|string',
            'user_id'  => 'nullable|exists:users,id',
            'admin_id' => 'nullable|integer',
        ]);

        if ($request->user()->role === 'admin') {
            $userId  = $validated['user_id'] ?? null;
            $adminId = $request->user()->id;
            $sender  = 'admin';
        } else {
            $userId  = $request->user()->id;
            $adminId = $validated['admin_id'] ?? null;
            $sender  = 'user';
        }

        if (!$userId) {
            return response()->json(['message' => 'Penerima pesan tidak valid.'], 422);
        }

        $chat = Chat::create([
            'user_id'  => $userId,
            'admin_id' => $adminId,
            'sender'   => $sender,
            'pesan'    => $validated['pesan'],
            'is_read'  => false,
        ]);

        return response()->json([
            'message' => 'Pesan terkirim',
            'data'    => new ChatResource($chat->load(['user', 'admin'])),
        ], 201);
    }

    /**
     * GET /api/admins — Ambil daftar admin support yang tersedia untuk diajak chat oleh user
     */
    public function getAdmins(Request $request): JsonResponse
    {
        $admins = User::where('role', 'admin')->get();

        if ($admins->isEmpty()) {
            $data = [
                [
                    'id'           => 1,
                    'name'         => 'Administrator Toko Buku',
                    'username'     => 'admin',
                    'foto'         => null,
                    'role_title'   => 'CS Support Utama',
                    'is_online'    => true,
                    'last_message' => 'Ada yang bisa saya bantu?',
                    'unread'       => 0,
                ]
            ];
            return response()->json(['data' => $data]);
        }

        $data = $admins->map(function ($admin, $index) use ($request) {
            $roleTitles = ['CS Support Utama', 'Layanan Kasir & Pesanan', 'Admin Customer Service'];
            $lastChat   = Chat::where('user_id', $request->user()->id)
                ->where(function ($q) use ($admin) {
                    $q->where('admin_id', $admin->id)->orWhereNull('admin_id');
                })
                ->latest()
                ->first();

            return [
                'id'           => $admin->id,
                'name'         => $admin->name ?? 'Admin BookStore',
                'username'     => $admin->username ?? 'admin',
                'foto'         => $admin->foto ? asset('storage/' . $admin->foto) : null,
                'role_title'   => $roleTitles[$index % count($roleTitles)],
                'is_online'    => true,
                'last_message' => $lastChat ? $lastChat->pesan : 'Ada yang bisa saya bantu?',
                'unread'       => 0,
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * POST /api/chats/delete-for-me
     * Hapus pesan hanya untuk diri sendiri (soft hide via chat_deletions).
     * Bisa berupa pesan milik sendiri ATAU pesan dari admin/lawan bicara.
     */
    public function deleteForMe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:chats,id',
        ]);

        $userId = $request->user()->id;

        // Insert deletion records (ignore duplicate if already hidden)
        foreach ($validated['ids'] as $chatId) {
            ChatDeletion::firstOrCreate([
                'chat_id' => $chatId,
                'user_id' => $userId,
            ]);
        }

        return response()->json([
            'message' => 'Pesan disembunyikan untuk Anda (' . count($validated['ids']) . ' pesan).',
        ]);
    }

    /**
     * POST /api/chats/delete-for-all
     * Hapus pesan untuk semua orang (hard delete).
     * Hanya boleh menghapus pesan milik sendiri (sender = user, user_id = me).
     */
    public function deleteForAll(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:chats,id',
        ]);

        $user = $request->user();

        // Only allow deleting own messages
        $ownIds = Chat::whereIn('id', $validated['ids'])
            ->where('user_id', $user->id)
            ->where('sender', 'user')
            ->pluck('id')
            ->toArray();

        if (count($ownIds) !== count($validated['ids'])) {
            return response()->json([
                'message' => 'Hapus untuk semua hanya bisa dilakukan pada pesan milik Anda sendiri.',
            ], 422);
        }

        $count = count($ownIds);
        Chat::whereIn('id', $ownIds)->delete();

        return response()->json([
            'message' => "Berhasil menghapus {$count} pesan untuk semua orang.",
        ]);
    }

    /**
     * DELETE /api/chats/{id} — Hapus satu pesan (legacy, hard delete by owner/admin)
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $chat = Chat::findOrFail($id);

        if ($request->user()->role !== 'admin' && $chat->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $chat->delete();

        return response()->json(['message' => 'Pesan berhasil dihapus']);
    }

    /**
     * POST /api/chats/delete-batch — Hapus banyak pesan sekaligus (legacy)
     */
    public function destroyBatch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:chats,id',
        ]);

        $query = Chat::whereIn('id', $validated['ids']);

        if ($request->user()->role !== 'admin') {
            $query->where('user_id', $request->user()->id);
        }

        $count = $query->count();
        $query->delete();

        return response()->json(['message' => "Berhasil menghapus {$count} pesan"]);
    }
}

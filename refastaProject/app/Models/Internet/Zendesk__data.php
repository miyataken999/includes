<?php

namespace App\Models\Internet;

use Illuminate\Support\Facades\DB;
use \Illuminate\Database\Eloquent\Model;

class Zendesk__data extends Model
{
//    protected $connection = "refasta_internet_editor";
    protected $table = "internet.zendesk__data";
    protected $primaryKey = "chat_id";

    protected $casts = [
      'open_flag' => 'boolean',
      'check_flag' => 'boolean',
    ];
    public function getChatsForList()
    {
        return Zendesk_chats::where('chat_id', $this->chat_id)
            ->where('open_flag', true)
            ->where('sender_type', '!=', 'NULL')
            ->where('sender_type', '!=', 'Trigger')
            ->orderBy('chat_id', 'asc')
            ->limit(5)
            ->get();
    }

    public function getFavCount()
    {
        return Members_favorite::where('category', '=', 'チャット')
            ->where('item_id', $this->chat_id)
            ->count();
    }
}

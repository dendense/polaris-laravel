<?php

namespace App\Http\Controllers;

use App\Message;
use App\User;
use Illuminate\Http\Request;

class MessagesController extends Controller
{
    /**
     * Protecting MessageController
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get this current user follower
     * 
     * @return \Illuminate\Http\Respons
     */
    public function get()
    {
        $contacts = User::where('id', '!=', auth()->id())->get();

        if (is_null($contacts)) {
            return response()->json([
                'status' => 'failed',
                'success' => false,
                'msg' => 'There is no contact'
            ]);   
        }
        
        return response()->json($contacts);
    }

    /**
     * Send messages to other user
     * 
     * @return \Illuminate\Http\Respons
     */
    public function send(Request $request)
    {

        $message = Message::create([
            'from' => auth()->id(),
            'to' => $request->input('contact_id'),
            'text' => $request->input('text')
        ]);

        if (!$message) {
            return response()->json([
                'status' => 'failed',
                'success' => false,
                'msg' => 'failed to send message'
            ]);
        }

        return response()->json($message);
    }

    /**
     * Receive messages from other user
     * 
     * @return \Illuminate\Http\Respons
     */
    public function getMessageFor($id)
    {
        $messages = Message::where('from', $id)->orWhere('to', $id)->get();

        $messages = Message::where(
            function ($query) use ($id) {
                $query->where('from', auth()->id());
                $query->where('to', $id);
        })->orWhere(
            function ($query) use ($id) {
                $query->where('from', $id);
                $query->where('to', auth()->id());
            }
        )->get();

        if (is_null($messages)) {
            return response()->json([
                'status' => 'failed',
                'success' => false,
                'msg' => 'there is no message'
            ]);
        }

        return response()->json($messages);
    }

    /**
     * Delete selected chat
     * 
     * @param $id
     * @return \Illuminate\Http\Respons
     */
    public function deleteChat($id)
    {
        $chats = Message::where(
            function ($query) use ($id) {
                $query->where('from', auth()->id());
                $query->where('to', $id);
        })->orWhere(
            function ($query) use ($id) {
                $query->where('from', $id);
                $query->where('to', auth()->id());
            }
        )->get();

        foreach ($chats as $chat) {
            $chat->delete();
        }
        if ($chats->isEmpty()) {

            return response()->json([
                'status' => 'failed',
                'success' => false,
                'msg' => 'Chat failed to delete'
            ]);
        }

        return response()->json([
            'status' => 200,
            'success' => true,
            'msg' => 'Chat deleted successfuly',
            'create_chat' => [
                'href' => 'api/v1/user/conversation/send',
                'param' => 'to, from, text',
                'method' => 'POST'
                ]
        ]);
    }

    /**
     * Delete selected messages
     * 
     * @param $id
     * @return \Illuminate\Http\Respons
     */
    public function deleteMessages($id)
    {
        $messages = Message::where('id', $id)->first();

        if (!$messages->delete()) {
            return response()->json([
                'status' => 'failed',
                'success' => false,
                'msg' => 'Chat failed to delete'
            ]);
        }
        return response()->json([
            'status' => 200,
            'success' => true,
            'msg' => 'message deleted successfuly',
            'send_message' => [
                'href' => 'api/v1/user/conversation/send',
                'param' => 'to, from, text',
                'method' => 'POST'
                ]
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatGptController extends Controller
{

    /**
     * index
     *
     * @param  Request  $request
     */
    public function index(Request $request)
    {
        return view('chat');
    }

    /**
     * chat
     *
     * @param  Request  $request
     */
    public function chat(Request $request)
    {
       // バリデーション
        $request->validate([
            'sentence' => 'required',
        ]);

        // 文章
        $sentence = $request->input('sentence');
        // dd($this->chat_gpt_memory("語尾に’ね’をつけて答えて", ["やば","すぎ","明日の天気は？"], ["たし","かに"]));

        // ChatGPT API処理
        $chat_response = $this->chat_gpt4("語尾に’ね’をつけて答えて", $sentence);

        return view('chat', compact('sentence', 'chat_response'));
    }

    /**
     * ChatGPT API呼び出し
     * cURL
     */
    function chat_gpt3($system, $user)
    {
        // ChatGPT APIのエンドポイントURL
        $url = "https://api.openai.com/v1/chat/completions";

        // APIキー
        $api_key = env('CHAT_GPT_KEY');

        // ヘッダー
        $headers = array(
            "Content-Type: application/json",
            "Authorization: Bearer $api_key"
        );

        // パラメータ
        $data = array(
            "model" => "gpt-3.5-turbo",
            "messages" => [
                [
                    "role" => "system",
                    "content" => $system
                ],
                [
                    "role" => "user",
                    "content" => $user
                ]
            ]
        );

        // cURLセッションの初期化
        $ch = curl_init();

        // cURLオプションの設定
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // リクエストの送信と応答結果の取得
        $response = json_decode(curl_exec($ch));

        // cURLセッションの終了
        curl_close($ch);

        // 応答結果の取得
        if (isset($response->error)) {
            // エラー
            return $response->error->message;
        }

        return $response->choices[0]->message->content;
    }


    /**
     * ChatGPT API呼び出し
     * cURL
     */
    function chat_gpt4($system, $user)
    {
        // ChatGPT APIのエンドポイントURL
        $url = "https://api.openai.com/v1/chat/completions";

        // APIキー
        $api_key = env('CHAT_GPT_KEY');
        $organization_id = env('CHAT_GPT_ORGANIZATION_ID');
        // ヘッダー
        $headers = array(
            "Content-Type: application/json",
            "Authorization: Bearer $api_key",
            "OpenAI-Organization: $organization_id"     
        );

        // パラメータ
        $data = array(
            "model" => "gpt-4",
            "messages" => [
                [
                    "role" => "system",
                    "content" => $system
                ],
                [
                    "role" => "user",
                    "content" => $user
                ]
            ]
        );

        // cURLセッションの初期化
        $ch = curl_init();

        // cURLオプションの設定
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // リクエストの送信と応答結果の取得
        $response = json_decode(curl_exec($ch));

        // cURLセッションの終了
        curl_close($ch);

        // 応答結果の取得
        if (isset($response->error)) {
            // エラー
            return $response->error->message;
        }

        return $response->choices[0]->message->content;
    }

    /**
     * ChatGPT API呼び出し
     * cURL
     * 記憶あり
     */
    function chat_gpt_memory($system, $users, $assistants)
    {
        // ChatGPT APIのエンドポイントURL
        $url = "https://api.openai.com/v1/chat/completions";

        // APIキー
        $api_key = env('CHAT_GPT_KEY');
        $organization_id = env('CHAT_GPT_ORGANIZATION_ID');
        // ヘッダー
        $headers = array(
            "Content-Type: application/json",
            "Authorization: Bearer $api_key",
            "OpenAI-Organization: $organization_id"     
        );

        $conversations = [array('role'=> 'system')+ array('content'=> $system),array('role'=> 'user')+ array('content'=> $users[0])];

        for ($i = 0; $i < count($users)-1; $i++)   {
            array_push($conversations,array('role'=> 'assistant')+ array('content'=> $assistants[$i]));
            array_push($conversations,array('role'=> 'user')+ array('content'=> $users[$i+1]));
        }     

        // パラメータ
        $data = array(
            "model" => "gpt-4",
            "messages" => $conversations
        );

        // cURLセッションの初期化
        $ch = curl_init();

        // cURLオプションの設定
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // リクエストの送信と応答結果の取得
        $response = json_decode(curl_exec($ch));

        // cURLセッションの終了
        curl_close($ch);

        // 応答結果の取得
        if (isset($response->error)) {
            // エラー
            return $response->error->message;
        }

        return $response->choices[0]->message->content;
    }
}

<?php

if (!isset($_REQUEST)) {
    return;
}

//Строка для подтверждения адреса сервера из настроек Callback API
$confirmation_token = 'маленький ключ';

//Ключ доступа сообщества
$token = 'большой ключ';

//Получаем и декодируем уведомление
$data = json_decode(file_get_contents('php://input'));


$keyboard_menu = [
  'one_time' => false,
  'buttons' => [
    [
      [
        'action' =>  
        [
          'type' => 'text',
          'payload' => '{"button": "1"}',
          'label' => 'Синяя кнопка',
        ],
        'color' => 'primary',
      ],
    ],
    [
      [
        'action' =>  
        [
          'type' => 'text',
          'payload' => '{"button": "2"}',
          'label' => 'Никакая кнопка',
        ],
        'color' => 'default',
      ],
    ],
    [
      [
        'action' =>  
        [
          'type' => 'text',
          'payload' => '{"button": "3"}',
          'label' => 'Красная кнопка',
        ],
        'color' => 'negative',
      ],
    ],
    [
      [
        'action' =>  
        [
          'type' => 'text',
          'payload' => '{"button": "3"}',
          'label' => 'Зелёная кнопка',
        ],
        'color' => 'positive',
      ],
    ],
  ],
];

//Проверяем, что находится в поле "type"
switch ($data->type) {
    //Если это уведомление для подтверждения адреса сервера...
    case 'confirmation':
        echo $confirmation_token;
        break;

    //Если это уведомление о новом сообщении...
    case 'message_new':
        //...получаем id его автора
       $peer_id = $data->object->peer_id;
       $user_id = $data->object->from_id; 
       $message = $data->object->text;
       $body = $data->object->body; 
       $from_id = $data->object->from_id;

        //и извлекаем из ответа его имя
       $user_info = json_decode(file_get_contents("https://api.vk.com/method/users.get?user_ids={$user_id}&access_token={$token}&v=5.0")); 
       $user_name = $user_info->response[0]->first_name;


        //С помощью messages.send и токена сообщества отправляем ответное сообщение
        $request_params = array( 
			  'message' => "{$user_name} - lol", 
			  'peer_id' => $peer_id, 
			  'access_token' => $token, 
			  'v' => '5.80',
			  'keyboard' => json_encode($keyboard_menu)
			 );
        

        $get_params = http_build_query($request_params);

        file_get_contents('https://api.vk.com/method/messages.send?' . $get_params);

        //Возвращаем "ok" серверу Callback API
        echo('ok');

        break;
}
?>

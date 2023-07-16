php
<?php

// Импортируем необходимые классы и модули
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Message;
use GuzzleHttp\Client;

// Создаем экземпляр бота
$telegram = new Api('#');

// Извлекаем обновления из Telegram
$updates = $telegram->getWebhookUpdates();

// Проверяем, что полученное сообщение является текстовым
if ($updates->getMessage()->getText()) {
    // Получаем текстовое сообщение
    $messageText = $updates->getMessage()->getText();
    
    // Если текст сообщения равен "/start"
    if ($messageText == '/start') {
        // Отправляем приветственное сообщение
        $telegram->sendMessage([
            'chat_id' => $updates->getMessage()->getChat()->getId(),
            'text' => 'Привет! Я бот "Погода Якутск". Какую погоду ты хочешь узнать?'
        ]);
    } else {
        // Если текст сообщения не равен "/start", получаем погоду в Якутске
        $weather = getWeather();

        // Отправляем сообщение с погодой
        $telegram->sendMessage([
            'chat_id' => $updates->getMessage()->getChat()->getId(),
            'text' => $weather
        ]);
    }
}

/**
 * Функция для получения погоды в Якутске
 * 
 * @return string Строка с информацией о погоде
 */
function getWeather()
{
    // Создаем клиент для отправки HTTP-запросов
    $client = new Client();
    
    // Отправляем GET-запрос к API погоды
    $response = $client->request('GET', 'https://api.weatherapi.com/v1/current.json?key=https://api.open-meteo.com/v1/forecast?latitude=62.0339&longitude=129.7331&hourly=temperature_2m');
    
    // Получаем тело ответа
    $body = $response->getBody();
    
    // Преобразуем JSON-строку в ассоциативный массив
    $data = json_decode($body, true);
    
    // Извлекаем необходимую информацию о погоде
    $temperature = $data['current']['temp_c'];
    $condition = $data['current']['condition']['text'];
    
    // Формируем строку с информацией о погоде
    $weatherString = "Сейчас в Якутске температура $temperature °C. $condition";
    
    return $weatherString;
}

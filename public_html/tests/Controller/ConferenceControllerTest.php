<?php
declare(strict_types=1);


namespace Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Panther\PantherTestCase;

//class ConferenceControllerTest extends WebTestCase // для имитации браузера
class ConferenceControllerTest extends PantherTestCase
{
    public function testIndex(): void
    {
        // $client предназначена для имитации браузера
        //$client = static::createClient();

        // использование реального браузера
        // Переменная окружения SYMFONY_DEFAULT_ROUTE_URL содержит URL-адрес локального веб-сервера
        $_SERVER['SYMFONY_DEFAULT_ROUTE_URL'] ??= 'https://localhost';
        $client = static::createPantherClient(['external_base_uri' => $_SERVER['SYMFONY_DEFAULT_ROUTE_URL']]);
        $client->request('GET', '/');

        // проверяет, что главная страница возвращает статус 200 в HTTP-ответе
        self::assertResponseIsSuccessful();

        // проверяет наличие на странице элемента
        self::assertSelectorTextContains('h2', 'Give your feedback');
    }

    public function testCommentSubmission(): void
    {
        $client = static::createClient();
        $client->request('GET', '/conference/amsterdam-2019');
        $client->submitForm('Submit', [
            'comment_form[author]' => 'Fabien',
            'comment_form[text]' => 'Some feedback from an automated functional test',
            'comment_form[email]' => 'me@automat.ed',
            'comment_form[photo]' => dirname(__DIR__, 2) . '/public/images/under-construction.gif',
        ]);

        self::assertResponseRedirects();
        $client->followRedirect();
        self::assertSelectorExists('div:contains("There are 2 comments")');
    }

    public function testConferencePage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertCount(2, $crawler->filter('h4'));
        $client->clickLink('View');

        self::assertPageTitleContains('Amsterdam');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h2', 'Amsterdam - 2019');
        self::assertSelectorExists('div:contains("There are 1 comments")');
    }
}
<?php
declare(strict_types=1);


namespace Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConferenceControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        // $client предназначена для имитации браузера
        $client = static::createClient();
        $client->request('GET', '/');

        // проверяет, что главная страница возвращает статус 200 в HTTP-ответе
        self::assertResponseIsSuccessful();

        // проверяет наличие на странице элемента
        self::assertSelectorTextContains('h2', 'Give your feedback');
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
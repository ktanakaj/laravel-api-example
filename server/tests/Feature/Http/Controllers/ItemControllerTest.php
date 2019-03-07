<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\Globals\User;

class ItemControllerTest extends TestCase
{
    /**
     * アイテム一覧のテスト。
     */
    public function testIndex() : void
    {
        $user = factory(User::class)->states('allitems')->create();

        // ページング条件なしで取得
        $response = $this->withLogin($user)->json('GET', "/items");
        $response
            ->assertStatus(200)
            ->assertJson([
                'perPage' => 20,
                'currentPage' => 1,
                'from' => 1,
            ]);

        $json = $response->json();
        $this->assertGreaterThan(0, $json['total']);
        $this->assertGreaterThan(0, $json['lastPage']);
        $this->assertGreaterThan(0, $json['to']);
        $this->assertGreaterThan(0, count($json['data']));

        $userItem = $json['data'][0];
        $this->assertArrayHasKey('id', $userItem);
        $this->assertArrayHasKey('itemId', $userItem);
        $this->assertArrayHasKey('count', $userItem);
        $this->assertArrayHasKey('createdAt', $userItem);
        $this->assertArrayHasKey('updatedAt', $userItem);

        // TODO: 0個のアイテムが参照されないこともテストする
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\TransformImagePathService\TransformImagePathService;
use Tests\TestCase;

class TransformImagePathServiceTest extends TestCase
{
    private TransformImagePathService $transformImagePathService;
    public function setUp(): void
    {
        parent::setUp();
        $this->transformImagePathService = new TransformImagePathService();
    }
    public function testGetDestinationPath(): void
    {
        $path = $this->transformImagePathService->getDestinationPath(
            '/storage/image/sku/11363395-8324817633335596.jpg',
            'small');

        $this->assertEquals('/storage/image/sku/small/11363395-8324817633335596.jpg', $path);
    }
}

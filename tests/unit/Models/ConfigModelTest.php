<?php
declare(strict_types=1);

use Codeception\TestCase\Test;
use GravApi\Models\ConfigModel;

final class ConfigModelTest extends Test
{
    /**
     * @var ConfigModel $model
     */
    protected $model;

    protected function _before()
    {
        $this->model = new ConfigModel('id', array('foo' => 'bar'));
    }

    public function testConfigModelHasStringId(): void
    {
        $this->assertIsString($this->model->id);
    }

    public function testConfigModelIdMatchesGivenId(): void
    {
        $this->assertEquals('id', $this->model->id);
    }

    public function testConfigModelHasArrayData(): void
    {
        $this->assertIsArray($this->model->data);
    }

    public function testConfigModelDataMatchesGivenData(): void
    {
        $this->assertEquals(
            array('foo' => 'bar'),
            $this->model->data
        );
    }
}

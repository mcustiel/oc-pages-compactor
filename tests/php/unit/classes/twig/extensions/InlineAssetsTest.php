<?php
namespace Mcustiel\CompactPages\Tests\Php\Unit\Classes\Twig\Extensions;

use Cms\Classes\Controller;
use Mcustiel\CompactPages\Classes\Twig\Extensions\InlineAssets;
use Illuminate\Support\Facades\Event;

/**
 * This class uses \PluginTestCase because base_path method is used
 * in the unit being tested and we need to mock a facade.
 */
class InlineAssetsTest extends \PluginTestCase
{
    /**
     * @var \Cms\Classes\Controller|\PHPUnit_Framework_MockObject_MockObject
     */
    private $controller;

    /**
     * @var \Mcustiel\CompactPages\Classes\Twig\Extensions\InlineAssets
     */
    private $extension;

    /**
     * @before
     */
    public function prepareControllerMock()
    {
        $this->controller = $this->getMockBuilder(Controller::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->extension = new InlineAssets($this->controller);
    }

    /**
     * @test
     */
    public function shouldReturnEmptyIfNotJsAssetsAdded()
    {
        $this->controller->expects($this->once())
            ->method('getAssetPaths')
            ->willReturn($this->getEmptyAssets());
        $this->assertEquals('', $this->extension->inlineScripts());
    }

    /**
     * @test
     */
    public function shouldReturnEmptyIfNotCssAssetsAdded()
    {
        $this->controller->expects($this->once())
            ->method('getAssetPaths')
            ->willReturn($this->getEmptyAssets());
        $this->assertEquals('', $this->extension->inlineStyles());
    }

    /**
     * @test
     */
    public function shouldReturnTheAssetsContents()
    {
        $this->controller->expects($this->once())
            ->method('getAssetPaths')
            ->willReturn($this->getFixtureAssets());
        $this->assertEquals(
            '<script type="text/javascript">' . PHP_EOL
            . "I am a JS File\n"
            . "I am another JS file\n" . PHP_EOL
            . '</script>',
            $this->extension->inlineScripts()
        );
    }

    /**
     * @test
     */
    public function shouldReturnTheModifiedAssetsFromTheEventCall()
    {
        $this->controller->expects($this->once())
            ->method('getAssetPaths')
            ->willReturn($this->getFixtureAssets());
        Event::shouldReceive('fire')
            ->once()
            ->with(
                'mcustiel.compactpages.assetInlining',
                [
                    '/plugins/mcustiel/compactpages/tests/php/fixtures/test.css',
                    "a {\n"
                    . " background: url(../../anImage.png);\n"
                    . "}\n",
                    'css'
                ]
            )
            ->andReturn('Modified the css');
        $this->assertEquals(
            '<style type="text/css">' . PHP_EOL
            . 'Modified the css' . PHP_EOL
            . '</style>',
            $this->extension->inlineStyles()
        );
    }

    /**
     * @test
     */
    public function shouldReplaceRelativePathsInsideCss()
    {
        $this->controller->expects($this->once())
        ->method('getAssetPaths')
        ->willReturn($this->getFixtureAssets());
        $this->assertEquals(
            '<style type="text/css">' . PHP_EOL
            . "a {\n"
            . " background: url(/plugins/mcustiel/compactpages/tests/anImage.png);\n"
            . "}\n" . PHP_EOL
            . '</style>',
            $this->extension->inlineStyles()
        );
    }

    private function getEmptyAssets()
    {
        return ['js' => [], 'css' => []];
    }

    private function getFixtureAssets()
    {
        return [
            'js' => [
                '/plugins/mcustiel/compactpages/tests/php/fixtures/test.js',
                '/plugins/mcustiel/compactpages/tests/php/fixtures/test2.js',
            ],
            'css' => [
                '/plugins/mcustiel/compactpages/tests/php/fixtures/test.css',
            ]
        ];
    }
}

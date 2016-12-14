<?php

namespace AppBundle\Tests\Twig;


use AppBundle\Twig\AppExtension;
use AppBundle\Utils\Markdown;

class AppExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldTransformMarkdownToHtml()
    {
        $markdownContent = 'markdown content';
        $locales = 'es';

        $markdown = $this->prophesize(Markdown::class);
        $markdown
            ->toHtml($markdownContent)
            ->willReturn('html')
            ->shouldBeCalled()
        ;

        $extension = new AppExtension($markdown->reveal(), $locales);

        $html = $extension->markdownToHtml($markdownContent);

        $this->assertSame('html', $html);
    }
}

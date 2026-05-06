<?php

namespace Sensiolabs\GotenbergBundle\Tests\Twig;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sensiolabs\GotenbergBundle\Builder\BuilderAssetInterface;
use Sensiolabs\GotenbergBundle\Twig\GotenbergExtension;
use Sensiolabs\GotenbergBundle\Twig\GotenbergRuntime;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\RuntimeLoader\FactoryRuntimeLoader;

class GotenbergExtensionTest extends TestCase
{
    /**
     * @return array{Environment, MockObject&BuilderAssetInterface}
     */
    private function createTwigEnvironment(): array
    {
        $builder = $this->createMock(BuilderAssetInterface::class);
        $builder->method('addAsset');

        $runtime = new GotenbergRuntime();
        $runtime->setBuilder($builder);

        $twig = new Environment(new ArrayLoader(), ['autoescape' => 'html']);
        $twig->addExtension(new GotenbergExtension());
        $twig->addRuntimeLoader(new FactoryRuntimeLoader([
            GotenbergRuntime::class => static fn () => $runtime,
        ]));

        return [$twig, $builder];
    }

    public function testFontFaceIsNotEscapedInHtmlContext(): void
    {
        [$twig] = $this->createTwigEnvironment();

        $template = $twig->createTemplate('<style>{{ gotenberg_font_face("foo.ttf", "my_font") }}</style>');
        $output = $template->render([]);

        $this->assertStringContainsString(
            '@font-face {font-family: "my_font";src: url("foo.ttf");}',
            $output,
            'gotenberg_font_face must not HTML-escape quotes when used inside a <style> tag.',
        );
    }

    public function testFontStyleTagIsNotEscapedInHtmlContext(): void
    {
        [$twig] = $this->createTwigEnvironment();

        $template = $twig->createTemplate('{{ gotenberg_font_style_tag("foo.ttf", "my_font") }}');
        $output = $template->render([]);

        $this->assertStringContainsString(
            '<style>@font-face {font-family: "my_font";src: url("foo.ttf");}</style>',
            $output,
        );
    }
}

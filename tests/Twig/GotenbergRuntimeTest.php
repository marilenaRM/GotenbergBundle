<?php

namespace Sensiolabs\GotenbergBundle\Tests\Twig;

use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sensiolabs\GotenbergBundle\Builder\BuilderAssetInterface;
use Sensiolabs\GotenbergBundle\Twig\GotenbergExtension;
use Sensiolabs\GotenbergBundle\Twig\GotenbergRuntime;
use Symfony\Component\Asset\Packages;
use Symfony\Component\AssetMapper\AssetMapperRepository;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\RuntimeLoader\FactoryRuntimeLoader;

class GotenbergRuntimeTest extends TestCase
{
    public function testGetAsset(): void
    {
        [$runtime, $builder] = $this->createRuntime();
        $builder->expects($this->once())->method('addAsset')->with('foo');

        $this->assertSame('foo', $runtime->getAssetUrl('foo'));
    }

    public function testGetAssetThrowsWhenBuilderIsNotSet(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('The gotenberg_asset function must be used in a Gotenberg context.');
        $runtime = new GotenbergRuntime();
        $runtime->getAssetUrl('foo');
    }

    public function testGetFontFace(): void
    {
        [$runtime, $builder] = $this->createRuntime();
        $builder->expects($this->once())->method('addAsset')->with('foo.ttf');

        $this->assertSame(
            '@font-face {font-family: "my_font";src: url("foo.ttf");}',
            $runtime->getFontFace('foo.ttf', 'my_font'),
        );
    }

    public function testGetFontStyleTag(): void
    {
        [$runtime, $builder] = $this->createRuntime();
        $builder->expects($this->once())->method('addAsset')->with('foo.ttf');

        $this->assertSame(
            '<style>@font-face {font-family: "my_font";src: url("foo.ttf");}</style>',
            $runtime->getFontStyleTag('foo.ttf', 'my_font'),
        );
    }

    public function testGetAssetUrlWhenAssetMapperRepository(): void
    {
        $builder = $this->createMock(BuilderAssetInterface::class);
        $builder->expects($this->once())
            ->method('addAsset')
            ->with('/image/result.png')
        ;

        $assetMapperRepository = $this->createMock(AssetMapperRepository::class);
        $assetMapperRepository->expects($this->once())
            ->method('find')
            ->with('image/origin.png')
            ->willReturn('/image/result.png')
        ;

        $runtime = new GotenbergRuntime(null, $assetMapperRepository);
        $runtime->setBuilder($builder);

        $path = $runtime->getAssetUrl('image/origin.png');

        $this->assertSame('result.png', $path);
    }

    public function testGetAssetUrlWhenPackages(): void
    {
        $builder = $this->createMock(BuilderAssetInterface::class);
        $builder->expects($this->once())
            ->method('addAsset')
            ->with('image/result.png')
        ;

        $packages = $this->createMock(Packages::class);
        $packages->expects($this->once())
            ->method('getUrl')
            ->with('image/origin.png')
            ->willReturn('/image/result.png')
        ;

        $runtime = new GotenbergRuntime($packages, null);
        $runtime->setBuilder($builder);

        $path = $runtime->getAssetUrl('image/origin.png');

        $this->assertSame('result.png', $path);
    }

    #[TestWith(['/build/front/my_file.css?v=abc123'])]
    #[TestWith(['/build/front/my_file.css#abc123'])]
    public function testGetAssetUrlWhenPackagesWithUrlSpecificCharactersString(string $assetPathUrl): void
    {
        $builder = $this->createMock(BuilderAssetInterface::class);
        $builder->expects($this->once())
            ->method('addAsset')
            ->with('build/front/my_file.css')
        ;

        $packages = $this->createMock(Packages::class);
        $packages->expects($this->once())
            ->method('getUrl')
            ->with('/build/front/my_file.css')
            ->willReturn($assetPathUrl)
        ;

        $runtime = new GotenbergRuntime($packages, null);
        $runtime->setBuilder($builder);

        $path = $runtime->getAssetUrl('/build/front/my_file.css');

        $this->assertSame('my_file.css', $path);
    }

    public function testGetAssetUrlWhenAssetMapperRepositoryAndPackages(): void
    {
        $builder = $this->createMock(BuilderAssetInterface::class);
        $builder->expects($this->once())
            ->method('addAsset')
            ->with('image/result.png')
        ;

        $packages = $this->createMock(Packages::class);
        $packages->expects($this->once())
            ->method('getUrl')
            ->with('image/origin.png')
            ->willReturn('/image/result.png')
        ;

        $assetMapperRepository = $this->createMock(AssetMapperRepository::class);
        $assetMapperRepository->expects($this->once())
            ->method('find')
            ->with('image/origin.png')
            ->willReturn(null)
        ;

        $runtime = new GotenbergRuntime($packages, $assetMapperRepository);
        $runtime->setBuilder($builder);

        $path = $runtime->getAssetUrl('image/origin.png');

        $this->assertSame('result.png', $path);
    }

    public function testGetAssetUrlWhenMissingAssetMapperRepositoryAndPackages(): void
    {
        $builder = $this->createMock(BuilderAssetInterface::class);
        $builder->expects($this->once())
            ->method('addAsset')
            ->with('image/origin.png')
        ;

        $runtime = new GotenbergRuntime(null, null);
        $runtime->setBuilder($builder);

        $path = $runtime->getAssetUrl('image/origin.png');

        $this->assertSame('origin.png', $path);
    }

    public function testFontFaceCssEscapeFilterBreaksCssRule(): void
    {
        [$twig] = $this->createTwigEnvironment();

        $output = $twig->createTemplate('<style>{{ gotenberg_font_face("foo.ttf", "my_font") | e("css") }}</style>')->render([]);

        // | e('css') encodes every non-alphanumeric character (e.g. @ → \40, { → \7B)
        // which destroys the structure of the CSS rule
        $this->assertStringNotContainsString('@font-face', $output);
        $this->assertStringContainsString('\40', $output);
    }

    public function testFontFaceIsNotEscapedInHtmlContext(): void
    {
        [$twig] = $this->createTwigEnvironment();

        $output = $twig->createTemplate('<style>{{ gotenberg_font_face("foo.ttf", "my_font") }}</style>')->render([]);

        $this->assertStringContainsString(
            '@font-face {font-family: "my_font";src: url("foo.ttf");}',
            $output,
            'gotenberg_font_face must not HTML-escape quotes when used inside a <style> tag.',
        );
    }

    public function testFontStyleTagIsNotEscapedInHtmlContext(): void
    {
        [$twig] = $this->createTwigEnvironment();

        $output = $twig->createTemplate('{{ gotenberg_font_style_tag("foo.ttf", "my_font") }}')->render([]);

        $this->assertStringContainsString(
            '<style>@font-face {font-family: "my_font";src: url("foo.ttf");}</style>',
            $output,
        );
    }

    public function testFontFaceEscapesMaliciousPath(): void
    {
        [$twig] = $this->createTwigEnvironment();

        $output = $twig->createTemplate('<style>{{ gotenberg_font_face("fonts/<script>alert(1).ttf", "my_font") }}</style>')->render([]);

        $this->assertStringNotContainsString('<script>', $output);
        $this->assertStringContainsString('&lt;script&gt;', $output);
    }

    public function testFontFaceEscapesMaliciousName(): void
    {
        [$twig] = $this->createTwigEnvironment();

        $output = $twig->createTemplate('<style>{{ gotenberg_font_face("foo.ttf", "</style><script>alert(\'xss\')</script>") }}</style>')->render([]);

        $this->assertStringNotContainsString('<script>', $output);
        $this->assertStringContainsString('&lt;script&gt;', $output);
    }

    public function testFontStyleTagEscapesMaliciousPath(): void
    {
        [$twig] = $this->createTwigEnvironment();

        $output = $twig->createTemplate('{{ gotenberg_font_style_tag("fonts/<script>alert(1).ttf", "my_font") }}')->render([]);

        $this->assertStringNotContainsString('<script>', $output);
        $this->assertStringContainsString('&lt;script&gt;', $output);
    }

    public function testFontStyleTagEscapesMaliciousName(): void
    {
        [$twig] = $this->createTwigEnvironment();

        $output = $twig->createTemplate('{{ gotenberg_font_style_tag("foo.ttf", "</style><script>alert(\'xss\')</script>") }}')->render([]);

        $this->assertStringNotContainsString('<script>', $output);
        $this->assertStringContainsString('&lt;script&gt;', $output);
    }

    /**
     * @return array{GotenbergRuntime, MockObject&BuilderAssetInterface}
     */
    private function createRuntime(): array
    {
        $builder = $this->createMock(BuilderAssetInterface::class);
        $runtime = new GotenbergRuntime();
        $runtime->setBuilder($builder);

        return [$runtime, $builder];
    }

    /**
     * @return array{Environment, MockObject&BuilderAssetInterface}
     */
    private function createTwigEnvironment(): array
    {
        [$runtime, $builder] = $this->createRuntime();
        $builder->method('addAsset');

        $twig = new Environment(new ArrayLoader(), ['autoescape' => 'html']);
        $twig->addExtension(new GotenbergExtension());
        $twig->addRuntimeLoader(new FactoryRuntimeLoader([
            GotenbergRuntime::class => static fn () => $runtime,
        ]));

        return [$twig, $builder];
    }
}

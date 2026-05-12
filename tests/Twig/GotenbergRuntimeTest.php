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
        $builder = $this->createMock(BuilderAssetInterface::class);
        $builder->expects($this->once())->method('addAsset')->with('foo');

        $runtime = new GotenbergRuntime();
        $runtime->setBuilder($builder);

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
        $builder = $this->createMock(BuilderAssetInterface::class);
        $builder->expects($this->once())->method('addAsset')->with('foo.ttf');

        $runtime = new GotenbergRuntime();
        $runtime->setBuilder($builder);

        $this->assertSame(
            '@font-face {font-family: "my_font";src: url("foo.ttf");}',
            $runtime->getFontFace('foo.ttf', 'my_font'),
        );
    }

    public function testGetFontStyleTag(): void
    {
        $builder = $this->createMock(BuilderAssetInterface::class);
        $builder->expects($this->once())->method('addAsset')->with('foo.ttf');

        $runtime = new GotenbergRuntime();
        $runtime->setBuilder($builder);

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

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideFontRenderingCases(): iterable
    {
        yield 'gotenberg_font_face renders correctly inside a style tag' => [
            '<style>{{ gotenberg_font_face("foo.ttf", "my_font") }}</style>',
            '<style>@font-face {font-family: "my_font";src: url("foo.ttf");}</style>',
        ];
        yield 'gotenberg_font_style_tag renders correctly in html context' => [
            '{{ gotenberg_font_style_tag("foo.ttf", "my_font") }}',
            '<style>@font-face {font-family: "my_font";src: url("foo.ttf");}</style>',
        ];
        yield 'gotenberg_font_face escapes html tags in path inside a style tag' => [
            '<style>{{ gotenberg_font_face("fonts/<script>alert(1).ttf", "my_font") }}</style>',
            '<style>@font-face {font-family: "my_font";src: url("&lt;script&gt;alert(1).ttf");}</style>',
        ];
        yield 'gotenberg_font_style_tag escapes html tags in path' => [
            '{{ gotenberg_font_style_tag("fonts/<script>alert(1).ttf", "my_font") }}',
            '<style>@font-face {font-family: "my_font";src: url("&lt;script&gt;alert(1).ttf");}</style>',
        ];
        yield 'gotenberg_font_face escapes html injection in name inside a style tag' => [
            '<style>{{ gotenberg_font_face("foo.ttf", "</style><script>alert(\'xss\')</script>") }}</style>',
            '<style>@font-face {font-family: "&lt;/style&gt;&lt;script&gt;alert(&#039;xss&#039;)&lt;/script&gt;";src: url("foo.ttf");}</style>',
        ];
        yield 'gotenberg_font_style_tag escapes html injection in name' => [
            '{{ gotenberg_font_style_tag("foo.ttf", "</style><script>alert(\'xss\')</script>") }}',
            '<style>@font-face {font-family: "&lt;/style&gt;&lt;script&gt;alert(&#039;xss&#039;)&lt;/script&gt;";src: url("foo.ttf");}</style>',
        ];
        yield 'applying e("css") filter to gotenberg_font_face output destroys the css rule structure' => [
            '<style>{{ gotenberg_font_face("foo.ttf", "my_font") | e("css") }}</style>',
            '<style>\40 font\2D face\20 \7B font\2D family\3A \20 \22 my\5F font\22 \3B src\3A \20 url\28 \22 foo\2E ttf\22 \29 \3B \7D </style>',
        ];
    }

    /**
     * @dataProvider provideFontRenderingCases
     */
    public function testFontRendering(string $template, string $expected): void
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

        $this->assertSame($expected, $twig->createTemplate($template)->render([]));
    }
}

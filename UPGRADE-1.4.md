# UPGRADE FROM 1.3.0 to 1.4.0

## Deprecations

### `ContentTrait`: header and footer methods moved to `PageMarginalTrait`

The `header()`, `headerRaw()`, `headerFile()`, `footer()`, `footerRaw()` and `footerFile()` methods have been moved out of `ContentTrait` into the new dedicated `PageMarginalTrait`. These methods are still accessible through `ContentTrait` in 1.4 but will be removed in 2.0.

If you use `ContentTrait` directly in your own builders, add `PageMarginalTrait` instead to handle headers and footers.

### `UrlPdfBuilder` and `UrlScreenshotBuilder`: `content*` methods deprecated

The `content()`, `contentRaw()` and `contentFile()` methods are deprecated on `UrlPdfBuilder` and `UrlScreenshotBuilder`. Since the page body is provided by the URL itself, these methods have no effect. Use `url()` or `route()` instead. They will be removed in 2.0.

# UPGRADE FROM 1.1.0 to 1.2.0

## BC Breaks

* `header()`, `headerRaw()`, `headerFile()`, `footer()`, `footerRaw()` and `footerFile()` have been removed from the public API of all screenshot builders (`HtmlScreenshotBuilder`, `MarkdownScreenshotBuilder`, `UrlScreenshotBuilder`). Gotenberg's screenshot API does not support custom HTML headers and footers. These methods had no effect.
